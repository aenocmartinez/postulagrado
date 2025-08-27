<?php

namespace App\Http\Controllers;

use Src\application\procesos\BuscarProcesoUseCase;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\infrastructure\controller\seguimiento\ProcesoSeguimientoController;
use Src\infrastructure\controller\seguimiento\SeguimientoIndexController;
use Src\shared\di\FabricaDeRepositoriosOracle;
use Src\shared\response\ResponsePostulaGrado;

class LaravelSeguimientoController extends Controller
{
    private ProcesoRepository $procesoRepo;
    private ProgramaRepository $programaRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private ActividadRepository $actividadRepo;
    private NotificacionRepository $notificacionRepo;

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
    }

    public function index()
    {
        $response = (new SeguimientoIndexController($this->procesoRepo, $this->nivelEducativoRepo))(); 

        return view('admisiones.seguimientos.index', [
            'procesos' => $response->getData()
        ]);
    }

    public function show($procesoID)
    {
        $response = (new ProcesoSeguimientoController(
            $this->procesoRepo, 
            $this->programaRepo,
            $this->actividadRepo,
            $this->notificacionRepo))->__invoke($procesoID);

        if ($response->getCode() != 200) {
            return redirect()->route('seguimientos.index')->with($response->getCode(), $response->getMessage());
        }

        // dd($response->getData());

        return view('admisiones.seguimientos.show', [
            'proceso' => $response->getData()
        ]);
    }    
}
