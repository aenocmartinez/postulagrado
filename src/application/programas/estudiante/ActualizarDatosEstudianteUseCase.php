<?php

namespace Src\application\programas\estudiante;

use Carbon\Carbon;
use DateTimeInterface;
use Src\application\procesos\notificaciones\EnviarNotificacionUseCase;
use Src\domain\Notificacion;
use Src\domain\proceso\Proceso;
use Src\domain\programa\contacto\Contacto;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarDatosEstudianteUseCase
{
    public function __construct(
        private EstudianteRepository $estudianteRepo,
        private ContactoRepository $contactoRepo,
        private NotificacionRepository $notificacionRepo,
    ) {}

    public function ejecutar(ActualizacionDatosDTO $datos): ResponsePostulaGrado
    {

        $enlace = $this->estudianteRepo->buscarEnlacePorID($datos->enlace_id);
        if (!$enlace) {
            return new ResponsePostulaGrado(404, 'El formulario al que intenta acceder no es válido. Verifique la dirección o solicite nuevamente el acceso en la Universidad.');
        }

        if (($enlace->acen_usado ?? 'N') === 'S') {
            return new ResponsePostulaGrado(409, 'Este formulario ya fue diligenciado anteriormente. Si necesita realizar una nueva actualización, solicite un nuevo acceso en la Universidad.');
        }

        if (!empty($enlace->acen_fechaexpira)) {
            $expira = $this->aCarbon($enlace->acen_fechaexpira);
            if ($expira->isPast()) {
                return new ResponsePostulaGrado(410, 'El formulario ya no está disponible porque ha superado su tiempo de vigencia. Por favor, solicite un nuevo acceso para continuar con el proceso.');
            }
        }

        $ok = $this->estudianteRepo->guardarDatosActualizados($datos);
        if (!$ok) {
            return new ResponsePostulaGrado(500, 'No fue posible guardar la actualización de datos.');
        }

        $contacto = $this->contactoRepo->buscarPorProgramaID($datos->programa_id);
        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(409, 'La información fue actualizada pero no se notificó al programa académico.');
        }          
        
        $proceso = new Proceso();
        $proceso->setId($datos->proceso_id);
        $this->crearNotificacionActualizacion($contacto, $proceso, $datos->codigo);
                        
        return new ResponsePostulaGrado(200, 'Datos actualizados correctamente.',[
                'enlace_id'  => $datos->enlace_id,
                'proceso_id' => $datos->proceso_id,
                'codigo'     => $datos->codigo,
            ]);
    }

    private function aCarbon(mixed $valor): Carbon
    {
        if ($valor instanceof DateTimeInterface) {
            return Carbon::instance($valor);
        }
        return Carbon::parse((string) $valor);
    }

    /**
     * Crea la notificación por actualización de datos.
     */
    private function crearNotificacionActualizacion(Contacto $contacto, Proceso $proceso, string $codigoEstudiante): void
    {
        $n = new Notificacion();
        $n->setAsunto('Postulagrado: Actualización de datos de estudiante');
        $n->setMensaje("El estudiante con código {$codigoEstudiante} actualizó el formulario. Por favor revisar.");
        $n->setFechaCreacion(now());
        $n->setCanal('Correo electrónico');
        $n->setDestinatarios($contacto->getEmail());
        $n->setEstado('ENVIADA');
        $n->setProceso($proceso);

        $this->notificacionRepo->crear($n);        
        (new EnviarNotificacionUseCase())->ejecutar($n);
    }

}
