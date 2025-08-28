<?php

namespace Src\application\programas;

use Illuminate\Support\Facades\Auth;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ObtenerDetalleEstudianteProcesoUseCase
{    
    public function __construct(
        private ProgramaRepository $programaRepo, 
        private ProcesoRepository $procesoRepo,
        private EstudianteRepository $estudianteRepo
        ){}

    /**
     * Retorna el detalle de un estudiante (por código) dentro de un proceso,
     * validando que el proceso pertenezca al programa del usuario actual.
     */
    public function ejecutar(int $procesoID, string $codigo): ResponsePostulaGrado
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $programa = $user->programaAcademico(); 

        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($procesoID, $programa->getId());
        if (!$programaProceso || !$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, 'Programa no asociado al proceso.');
        }

        $resultado = $this->estudianteRepo->buscarEstudiantePorCodigo($codigo);
        if (!$resultado) {
            return new ResponsePostulaGrado(404, 'Estudiante no encontrado.');
        }

        $estudiante = $resultado[0];

        $det = $encontrado['detalle'] ?? (object)[];

        $primerNombre = $estudiante->primer_nombre;
        $segundoNombre = $estudiante->segundo_nombre;
        $primerApellido = $estudiante->primer_apellido;
        $segundoApellido = $estudiante->segundo_apellido;
        $nombreCompuesto = trim(implode(' ', array_filter([$primerNombre, $segundoNombre, $primerApellido, $segundoApellido])));

        $generoCode = strtoupper((string) $estudiante->genero);
        $generoTxt  = match ($generoCode) {
            'F' => 'Femenino',
            'M' => 'Masculino',
            'X' => 'No binario / Otro',
            default => $generoCode,
        };

        $creditosAprobados = (int) data_get($estudiante, 'cred_aprobados', 0);
        // Si quisieras derivar a partir de pensum y pendientes:
        // $pensum  = (int) data_get($det, 'creditos_pensum', 0);
        // $pend    = (int) data_get($det, 'cred_pendientes', 0);
        // $creditosAprobados = $creditosAprobados ?: max(0, $pensum - $pend);

        // Estado paz y salvo (si no existe, todos "Pendiente")
        $paz = [
            'financiera' => data_get($estudiante, 'paz_salvo.financiera', 'Pendiente'),
            'admisiones' => data_get($estudiante, 'paz_salvo.admisiones', 'Pendiente'),
            'biblioteca' => data_get($estudiante, 'paz_salvo.biblioteca', 'Pendiente'),
            'recursos'   => data_get($estudiante, 'paz_salvo.recursos', 'Pendiente'),
            'idiomas'    => data_get($estudiante, 'paz_salvo.idiomas', 'Pendiente'),
        ];

        $payload = [
            'nombre'                    => $nombreCompuesto,
            'primerNombre'              => (string) data_get($estudiante, 'primer_nombre', false),
            'segundoNombre'             => (string) data_get($estudiante, 'segundo_nombre', false),
            'primerApellido'            => (string) data_get($estudiante, 'primer_apellido', false),
            'segundoApellido'           => (string) data_get($estudiante, 'segundo_apellido', false),
            'programa'                  => mb_strtoupper($programa->getNombre(), 'UTF-8'),
            'creditos'                  => $creditosAprobados,
            'formularioActualizado'     => (bool) data_get($estudiante, 'formulario_actualizado', false),
            'esEgresado'                => (bool) data_get($estudiante, 'es_egresado', false),
            'representante'             => (bool) data_get($estudiante, 'representante', false),
            'genero'                    => $generoTxt,
            'hijoFuncionario'           => (bool) data_get($estudiante, 'hijo_funcionario', false),
            'universidadPregrado'       => (string) data_get($estudiante, 'universidad_pregrado', ''),
            'correo'                    => (string) (data_get($estudiante, 'correo') ?? data_get($estudiante, 'email_institucional', '')),
            'telefono'                  => (string) data_get($estudiante, 'telefono', ''),
            'documento'                 => (string) (data_get($estudiante, 'documento', '') ?: data_get($estudiante, 'numero_documento', '')),
            'tipoDocumento'             => (string) data_get($estudiante, 'tipo_documento_nombre', false),
            'lugarExpedicion'           => (string) data_get($estudiante, 'lugar_expedicion', false),
            'esHijoFuncionario'         => false,
            'esHijoDocente'             => false,
            'esFuncionarioUniversidad'  => false,
            'documentoURL'              => (string) data_get($estudiante, 'documento_url', '#'),
            'esPostgrado'               => (bool) data_get($estudiante, 'es_postgrado', false),
            'pazSalvo'                  => $paz,
        ];

        return new ResponsePostulaGrado(200, 'OK', $payload);
    }
}
