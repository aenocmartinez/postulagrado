<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\infrastructure\controller\programa\AgregarCandidatosController;
use Src\infrastructure\controller\programa\BuscarCandidatosController;
use Src\infrastructure\controller\programa\BuscarEstudianteController;
use Src\infrastructure\controller\programa\EnviarEnlaceActualizacionDatosEstudiantesController;
use Src\infrastructure\controller\programa\ObtenerDetalleEstudianteController;
use Src\infrastructure\controller\programa\QuitarEstudianteController;
use Src\infrastructure\controller\programa\SeguimientoProgramaProcesoController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelProgramaController extends Controller
{

    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private NotificacionRepository $notificacionRepo;
    private ActividadRepository $actividadRepo;
    private ProgramaRepository $programaRepo;
    private EstudianteRepository $estudianteRepo;
    private EnlaceActualizacionRepository $enlaceActualizacionRepo;

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
        $this->estudianteRepo = FabricaDeRepositoriosOracle::getInstance()->getEstudianteRepository();
        $this->enlaceActualizacionRepo = FabricaDeRepositoriosOracle::getInstance()->getEnlaceActualizacionRepository();
    }

    public function dashboard()
    {               
        return view('programa_academico.dashboard.index');
    }    

    public function procesos()
    {
        $response = (new ListarProcesoController($this->procesoRepo, $this->nivelEducativoRepo))->__invoke();
        return view('programa_academico.procesos.index', [
            'procesos' => $response->getData(),
        ]);
    }

    public function seguimientoProceso($procesoID)
    {
        $response = (new SeguimientoProgramaProcesoController(
            $this->procesoRepo, 
            $this->nivelEducativoRepo, 
            $this->notificacionRepo,
            $this->actividadRepo,
            $this->programaRepo
            ))->__invoke($procesoID);

        return view('programa_academico.procesos.seguimiento', [
            'seguimiento' => $response->getData()
        ]);            
    }  
    
    public function buscarEstudiantesCandidatosAGrado(int $codigoPrograma, int $anio, int $periodo)
    {
        /** @var App\Models\User $user */
        $user = Auth::user();

        $response = (new BuscarCandidatosController($this->programaRepo))
                    ->__invoke(
                        $user->programaAcademico()->getCodigo(),
                        $anio, 
                        $periodo
                    );

        return response()->json([
                'code' => $response->getCode(),
                'message' => $response->getMessage(),
                'data' => $response->getData()
            ], $response->getCode());

    }

    public function asociarEstudiantesCandidatosAProcesoGrado(Request $request)
    {
        $validated = $request->validate([
            'estudiantes'   => ['required', 'array', 'min:1'],
            'estudiantes.*' => ['required', 'string', 'max:20'],
            'proc_id'       => ['required', 'integer'],
            'anio'          => ['required', 'integer'],
            'periodo'       => ['required', 'integer'],
        ]);

        $estudiantes   = $validated['estudiantes'];
        $procesoID     = $validated['proc_id'];
        $anio          = $validated['anio'];
        $periodo       = $validated['periodo'];

        $response = (new AgregarCandidatosController($this->programaRepo, $this->procesoRepo))->__invoke(
            $estudiantes,
            $procesoID,
            $anio,
            $periodo
        );

        return response()->json([
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'data' => $response->getData()
        ], $response->getCode());        

    }    
    
    public function quitarEstudiante(int $estudianteProcesoProgramaID)
    {   
        $response = (new QuitarEstudianteController(
            $this->procesoRepo,
            $this->programaRepo
        ))->__invoke($estudianteProcesoProgramaID);

        return response()->json([
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'data' => $response->getData()
        ], $response->getCode());
        
    }
    
    public function buscarEstudiante(Request $request)
    {
        $documentoOrCodigo = $request->get('termino');

        $response = (new BuscarEstudianteController($this->estudianteRepo))->__invoke($documentoOrCodigo);

        return response()->json($response->getData());
    } 
    
    public function agregarUnEstudianteAProceso(Request $request)
    {
        [$code, $message] = $this->asociarCandidato(
            (int)$request->proceso_id,
            (string)$request->codigo,
            2024,
            1
        );

        $payload = ['code' => $code, 'message' => $message];

        if ($code === 200) {

            /** @var App\Models\User $user */
            $user = Auth::user();
            $programa = $user->programaAcademico();

            $est     = $this->construirEstParaFila((int)$request->proceso_id, $programa->getId(), (string)$request->codigo);
            $payload['row_html'] = view('programa_academico.procesos.partials.fila_estudiante_vinculado', compact('est'))->render();
        }

        return response()->json($payload, $code);
    }   
    
    private function asociarCandidato(int $procesoId, string $codigo, int $anio, int $periodo): array
    {
        $response = (new AgregarCandidatosController($this->programaRepo, $this->procesoRepo))->__invoke(
            [$codigo],
            $procesoId,
            $anio,
            $periodo
        );

        return [(int)$response->getCode(), $response->getMessage()];
    }  
    
    private function construirEstParaFila(int $procesoId, int $programaId, string $codigo): array
    {
        $ppesId = $this->estudianteRepo->findPpesId($procesoId, $programaId, $codigo);
        if (!$ppesId) {
            throw new \RuntimeException("No existe PPES_ID para {$codigo} en proceso {$procesoId}/programa {$programaId}");
        }

        $fila = $this->estudianteRepo->buscarEstudiantePorCodigo($codigo)[0] ?? [];
        $det  = (array) $fila;

        return [
            'ppes_id'     => $ppesId,              
            'estu_codigo' => (string) $codigo,
            'detalle'     => (object)[
                'pensum_estud'    => $det['pensum_estud']    ?? ($det['pensum'] ?? '-'),
                'documento'       => $det['documento']       ?? '-',
                'nombres'         => $det['nombres']         ?? ($det['nombre'] ?? '-'),
                'categoria'       => $det['categoria']       ?? '-',
                'situacion'       => $det['situacion']       ?? '-',
                'cred_pendientes' => $det['cred_pendientes'] ?? ($det['numeroCreditosPendientes'] ?? '-'),
            ],
        ];
    }

    /**
     * GET /programa-academico/estudiantes/{procesoId}/{codigo}
     * Retorna el detalle de un estudiante vinculado a un proceso.
     */
    public function detalleEstudianteProceso(int $procesoId, string $codigo)
    {
        try {

            $response = (new ObtenerDetalleEstudianteController(
                $this->programaRepo,
                $this->procesoRepo,
                $this->estudianteRepo,
            ))->__invoke($procesoId, $codigo);

            return response()->json([
                'code'    => $response->getCode(),
                'message' => $response->getMessage(),
                'data'    => $response->getData(),
            ], $response->getCode());

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'code'    => 500,
                'message' => 'Error al obtener el detalle del estudiante.',
            ], 500);
        }
    }
    
    public function enviarEnlaceActualizacionAEstudiantes()
    {
        $procesoID = request()->get('proceso_id');

        $response = (new EnviarEnlaceActualizacionDatosEstudiantesController(
            $this->programaRepo,
            $this->procesoRepo,
            $this->enlaceActualizacionRepo
        ))->__invoke($procesoID);

        return response()->json([
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
        ], $response->getCode());
    }    
  
}
