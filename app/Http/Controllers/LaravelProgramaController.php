<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\infrastructure\controller\programa\AgregarCandidatosController;
use Src\infrastructure\controller\programa\BuscarCandidatosController;
use Src\infrastructure\controller\programa\BuscarEstudianteController;
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

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
        $this->estudianteRepo = FabricaDeRepositoriosOracle::getInstance()->getEstudianteRepository();
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
}
