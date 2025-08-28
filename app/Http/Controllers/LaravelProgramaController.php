<?php

namespace App\Http\Controllers;

use DragonCode\Contracts\Cashier\Auth\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\infrastructure\controller\programa\BuscarCandidatosController;
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

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
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
        $user = FacadesAuth::user();

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
}
