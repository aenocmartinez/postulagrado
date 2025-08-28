<?php

namespace Src\application\programas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Src\domain\EnlaceActualizacion;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Shared\Notifications\GestorNotificaciones;
use Src\Shared\Notifications\MensajesPersonalizados;
use Src\Shared\Notifications\NotificacionDTO;
use Src\shared\response\ResponsePostulaGrado;

class EnviarEnlaceActualizacionUseCase
{
    protected GestorNotificaciones $gestorNotificaciones;
    private int $diasExpiracion = 15;

    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo,
        private EnlaceActualizacionRepository $enlaceRepo
    ) {
        $this->gestorNotificaciones = new GestorNotificaciones();
        $this->programaRepo = $programaRepo;
        $this->procesoRepo = $procesoRepo;
        $this->enlaceRepo = $enlaceRepo;
    }

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $programa = $user->programaAcademico();

        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($procesoID, $programa->getId());
        if (!$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, "Programa no asociado al proceso.");
        }

        $estudiantes = $this->programaRepo->listarEstudiantesCandidatos($programa->getId(), $procesoID);

        $total = count($estudiantes);
        $enviados = 0;
        $errores = 0;
        $fallidos = [];

        foreach ($estudiantes as $estudiante) {

            $datosEstudiante = (object)$estudiante['detalle'];
            $codigo = (string) ($datosEstudiante->estp_codigomatricula ?? '');
            $email  = (string) ($datosEstudiante->email_institucional ?? '');
            $nombre = (string) ($datosEstudiante->nombres ?? 'Estudiante');

            if (!$codigo || !$email) {
                $errores++;
                $fallidos[] = ['codigo' => $codigo, 'email' => $email, 'motivo' => 'Falta código o email'];
                continue;
            }

            try {                
                $token = $this->emitirToken($procesoID, $codigo);
                $urlFormulario = $this->construirUrlFormulario($token);

                $mensajeHtml = MensajesPersonalizados::generarHtmlEnlaceActualizacion($urlFormulario, $nombre);

                $notificacionDTO = new NotificacionDTO(
                    'Actualización de información personal – Proceso de postulación a grado',
                    $mensajeHtml,
                    [$email],
                    ['mailtrap']
                );

                $this->gestorNotificaciones->enviarNotificacion($notificacionDTO);
                $enviados++;

            } catch (\Throwable $e) {
                $errores++;
                $fallidos[] = ['codigo' => $codigo, 'email' => $email, 'motivo' => $e->getMessage()];
            }
        }

        $mensaje = "Solicitudes enviadas: {$enviados}/{$total}";
        if ($errores > 0) {
            $mensaje .= " | Errores: {$errores}";
        }

        return new ResponsePostulaGrado(200, $mensaje, [
            'total'   => $total,
            'enviados'=> $enviados,
            'errores' => $errores,
            'fallidos'=> $fallidos,
        ]);
    }

    /**
     * Reutiliza un token vigente y no usado; si no existe, crea uno nuevo.
     */
    private function emitirToken(int $procesoID, string $codigoEstudiante): string
    {
        $vigente = $this->enlaceRepo->buscarPorCodigoEstudianteYProceso($codigoEstudiante, $procesoID);

        $ahora = Carbon::now();
        $exp   = $vigente?->getFechaExpira();
        $usado = strtoupper($vigente?->getUsado() ?? 'N');

        $estaVigente = $vigente
            && $usado === 'N'
            && (!$exp || $ahora->lt(Carbon::parse($exp)));

        if ($estaVigente) {
            return $vigente->getToken();
        }

        // Generar nuevo token y persistir
        $token = Str::uuid()->toString();
        $fechaExpira = Carbon::now()->addDays($this->diasExpiracion)->format('Y-m-d H:i:s');

        $enlace = (new EnlaceActualizacion())
            ->setProcesoID($procesoID)
            ->setCodigoEstudiante($codigoEstudiante)
            ->setToken($token)
            ->setUsado('N')
            ->setFechaExpira($fechaExpira)
            ->setFechaUso(''); 

        $ok = $this->enlaceRepo->guardar($enlace);
        if (!$ok) {
            throw new \RuntimeException('No fue posible registrar el enlace de actualización.');
        }

        return $token;
    }

    /**
     * Construye la URL pública usando la ruta del formulario con token.
     * Asegúrate de tener: Route::get('/actualizar-datos/{token}', ...)->name('actualizacion.form.token');
     */
    private function construirUrlFormulario(string $token): string
    {
        return route('actualizacion.form.token', ['token' => $token]);
    }
}
