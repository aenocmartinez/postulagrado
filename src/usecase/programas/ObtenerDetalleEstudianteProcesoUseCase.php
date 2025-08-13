<?php

namespace Src\usecase\programas;

use Illuminate\Support\Facades\Auth;
use Src\repositories\ProgramaRepository;
use Src\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ObtenerDetalleEstudianteProcesoUseCase
{
    private ProgramaRepository $programaRepo;
    private ProcesoRepository $procesoRepo;

    public function __construct(ProgramaRepository $programaRepo, ProcesoRepository $procesoRepo)
    {
        $this->programaRepo = $programaRepo;
        $this->procesoRepo  = $procesoRepo;
    }

    /**
     * Retorna el detalle de un estudiante (por código) dentro de un proceso,
     * validando que el proceso pertenezca al programa del usuario actual.
     */
    public function ejecutar(int $procesoID, string $codigo): ResponsePostulaGrado
    {
        /** @var \App\Models\User $user */
        $user     = Auth::user();
        $programa = $user->programaAcademico(); // Objeto de dominio Programa

        // 1) Validar que el proceso pertenezca al programa del usuario
        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($procesoID, $programa->getId());
        if (!$programaProceso || !$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, 'Programa no asociado al proceso.');
        }

        $candidatos = $programa->listarEstudiantesCandidatos($procesoID);

        $encontrado = null;
        foreach ($candidatos as $row) {
            $codTop     = $row['estu_codigo']      ?? null;
            $codDetalle = data_get($row, 'detalle.estp_codigomatricula');

            if ((string)$codTop === (string)$codigo || (string)$codDetalle === (string)$codigo) {
                $encontrado = $row;
                break;
            }
        }

        if (!$encontrado) {
            return new ResponsePostulaGrado(404, 'Estudiante no vinculado a este proceso.');
        }

        $det = $encontrado['detalle'] ?? (object)[];

        $pn = trim((string) data_get($det, 'primer_nombre', ''));
        $sn = trim((string) data_get($det, 'segundo_nombre', ''));
        $pa = trim((string) data_get($det, 'primer_apellido', ''));
        $sa = trim((string) data_get($det, 'segundo_apellido', ''));
        $nombreCompuesto = trim(implode(' ', array_filter([$pn, $sn, $pa, $sa])));

        if ($nombreCompuesto === '') {
            $nombreCompuesto = trim((string) data_get($det, 'nombres', ''));
        }

        $generoCode = strtoupper((string) data_get($det, 'genero', ''));
        $generoTxt  = match ($generoCode) {
            'F' => 'Femenino',
            'M' => 'Masculino',
            'X' => 'No binario / Otro',
            default => $generoCode, // si ya viene “Masculino”, “Femenino”, etc.
        };

        $creditosAprobados = (int) data_get($det, 'cred_aprobados', 0);
        // Si quisieras derivar a partir de pensum y pendientes:
        // $pensum  = (int) data_get($det, 'creditos_pensum', 0);
        // $pend    = (int) data_get($det, 'cred_pendientes', 0);
        // $creditosAprobados = $creditosAprobados ?: max(0, $pensum - $pend);

        // Estado paz y salvo (si no existe, todos "Pendiente")
        $paz = [
            'financiera' => data_get($det, 'paz_salvo.financiera', 'Pendiente'),
            'admisiones' => data_get($det, 'paz_salvo.admisiones', 'Pendiente'),
            'biblioteca' => data_get($det, 'paz_salvo.biblioteca', 'Pendiente'),
            'recursos'   => data_get($det, 'paz_salvo.recursos', 'Pendiente'),
            'idiomas'    => data_get($det, 'paz_salvo.idiomas', 'Pendiente'),
        ];

        $payload = [
            'nombre'               => $nombreCompuesto,
            'programa'             => $programa->getNombre(),
            'creditos'             => $creditosAprobados,
            'formularioActualizado'=> (bool) data_get($det, 'formulario_actualizado', false),
            'esEgresado'           => (bool) data_get($det, 'es_egresado', false),
            'representante'        => (bool) data_get($det, 'representante', false),
            'genero'               => $generoTxt,
            'hijoFuncionario'      => (bool) data_get($det, 'hijo_funcionario', false),
            'universidadPregrado'  => (string) data_get($det, 'universidad_pregrado', ''),
            'correo'               => (string) (data_get($det, 'correo') ?? data_get($det, 'email_institucional', '')),
            'telefono'             => (string) data_get($det, 'telefono', ''),
            'documento'            => (string) (data_get($det, 'documento', '') ?: data_get($det, 'numero_documento', '')),
            'documentoURL'         => (string) data_get($det, 'documento_url', '#'),
            'esPostgrado'          => (bool) data_get($det, 'es_postgrado', false),
            'pazSalvo'             => $paz,
        ];

        return new ResponsePostulaGrado(200, 'OK', $payload);
    }
}
