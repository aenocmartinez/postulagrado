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
        private ProgramaRepository  $programaRepo, 
        private ProcesoRepository   $procesoRepo,
        private EstudianteRepository $estudianteRepo
    ) {}

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
        if (empty($resultado)) {
            return new ResponsePostulaGrado(404, 'Estudiante no encontrado.');
        }

        // Primer registro devuelto por el repo (stdClass)
        $estudiante = (object) $resultado[0];

        // Nombre compuesto
        $primerNombre    = (string) data_get($estudiante, 'primer_nombre', '');
        $segundoNombre   = (string) data_get($estudiante, 'segundo_nombre', '');
        $primerApellido  = (string) data_get($estudiante, 'primer_apellido', '');
        $segundoApellido = (string) data_get($estudiante, 'segundo_apellido', '');
        $nombreCompuesto = trim(implode(' ', array_filter([$primerNombre, $segundoNombre, $primerApellido, $segundoApellido])));

        // Género
        $generoCode = strtoupper((string) data_get($estudiante, 'genero', ''));
        $generoTxt  = match ($generoCode) {
            'F' => 'Femenino',
            'M' => 'Masculino',
            'X' => 'No binario / Otro',
            default => $generoCode,
        };

        // Créditos
        $creditosAprobados = (int) data_get($estudiante, 'cred_aprobados', 0);

        // Teléfono: prioriza el del formulario; si no, el de PEGE
        $telefono = (string) (data_get($estudiante, 'telefono_form') ?: data_get($estudiante, 'telefono', ''));

        // Paz y salvo (si no existe, “Pendiente”)
        $paz = [
            'financiera' => data_get($estudiante, 'paz_salvo.financiera', 'Pendiente'),
            'admisiones' => data_get($estudiante, 'paz_salvo.admisiones', 'Pendiente'),
            'biblioteca' => data_get($estudiante, 'paz_salvo.biblioteca', 'Pendiente'),
            'recursos'   => data_get($estudiante, 'paz_salvo.recursos', 'Pendiente'),
            'idiomas'    => data_get($estudiante, 'paz_salvo.idiomas', 'Pendiente'),
        ];

        // Flags provenientes de ESTUDIANTE_DATOS (aliases en el SQL)
        $hijoFuncionario     = (bool) data_get($estudiante, 'hijo_funcionario', false);
        $hijoDocente         = (bool) data_get($estudiante, 'hijo_docente', false);
        $funcionarioUCMC     = (bool) data_get($estudiante, 'funcionario_universidad', false);
        $docenteUCMC         = (bool) data_get($estudiante, 'docente_universidad', false);
        $grupoPertenece      = (bool) data_get($estudiante, 'grupo_investigacion_pertenece', false);
        $grupoNombre         = (string) data_get($estudiante, 'grupo_investigacion_nombre', '');
        $codigoSaberProTYT   = (string) data_get($estudiante, 'codigo_saber_pro', '');
        $certSaberProURL     = (string) data_get($estudiante, 'certificado_saber_pro_url', '');
        $departamento        = mb_strtoupper(trim((string) data_get($estudiante, 'departamento', '')), 'UTF-8');
        $ciudad              = mb_strtoupper(trim((string) data_get($estudiante, 'ciudad', '')), 'UTF-8');
        $direccion           = (string) data_get($estudiante, 'direccion', '');

        $uni        = mb_strtoupper(trim((string) data_get($estudiante, 'universidad_pregrado', '')), 'UTF-8');
        $esEgresado = ($uni === 'UNIVERSIDAD-COLEGIO MAYOR DE CUNDINAMARCA');

        // Documento identidad (la consulta ya puede normalizar con '/'; si no, aquí lo aseguramos)
        $docUrl = (string) data_get($estudiante, 'documento_url', '');
        if ($docUrl !== '' && $docUrl[0] !== '/') {
            $docUrl = '/'.$docUrl;
        }
        $docUrl = $docUrl !== '' ? $docUrl : '';

        $payload = [
            'nombre'                   => $nombreCompuesto,
            'primerNombre'             => $primerNombre,
            'segundoNombre'            => $segundoNombre,
            'primerApellido'           => $primerApellido,
            'segundoApellido'          => $segundoApellido,

            'programa'                 => mb_strtoupper($programa->getNombre(), 'UTF-8'),
            'creditos'                 => $creditosAprobados,
            'formularioActualizado'    => (bool) data_get($estudiante, 'formulario_actualizado', false),

            // Correos (institucional estrictamente; personal aparte)
            'correoInstitucional'      => (string) data_get($estudiante, 'email_institucional', ''),
            'correoPersonal'           => (string) data_get($estudiante, 'correo', ''),
            // Compatibilidad: 'correo' también será el institucional
            'correo'                   => (string) data_get($estudiante, 'email_institucional', ''),
            'direccion'                => (string) ($direccion ? $direccion . "  {$ciudad}, {$departamento}" : "-"),

            'telefono'                 => $telefono,
            'documento'                => (string) (data_get($estudiante, 'documento', '') ?: data_get($estudiante, 'numero_documento', '')),
            'tipoDocumento'            => (string) data_get($estudiante, 'tipo_documento_nombre', ''),
            'lugarExpedicion'          => (string) data_get($estudiante, 'lugar_expedicion', ''),

            // Flags y datos solicitados en la UI
            'hijoFuncionario'          => $hijoFuncionario,
            'hijoDocente'              => $hijoDocente,
            'funcionarioUCMC'          => $funcionarioUCMC,
            'docenteUCMC'              => $docenteUCMC,

            // (Compatibilidad con claves antiguas si las usa el front)
            'esHijoFuncionario'        => $hijoFuncionario,
            'esHijoDocente'            => $hijoDocente,
            'esFuncionarioUniversidad' => $funcionarioUCMC,

            // Grupo de investigación
            'grupoInvestigacion'       => [
                'pertenece' => $grupoPertenece,
                'nombre'    => $grupoNombre,
            ],

            // Saber Pro / TyT
            'codigoSaberProTYT'        => $codigoSaberProTYT,
            'certificadoSaberProURL'   => $certSaberProURL,

            // Documento identidad
            'documentoURL'             => $docUrl,

            // Otros
            'universidadPregrado'      => (string) data_get($estudiante, 'universidad_pregrado', ''),
            'tituloPregrado'           => (string) data_get($estudiante, 'titulo_pregrado', ''),
            'fechaGradoPregrado'       => (string) data_get($estudiante, 'fecha_grado_pregrado', ''),
            'esPostgrado'              => (bool) data_get($estudiante, 'es_postgrado', true),
            'esEgresado'               => (bool) $esEgresado,
            'genero'                   => $generoTxt,
            'pazSalvo'                 => $paz,
        ];

        return new ResponsePostulaGrado(200, 'OK', $payload);
    }
}
