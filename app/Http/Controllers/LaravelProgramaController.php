<?php

namespace App\Http\Controllers;

use Src\domain\NivelEducativo;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\infrastructure\controller\programa\SeguimientoProgramaProcesoController;
use Src\shared\di\FabricaDeRepositoriosOracle;
use Src\Shared\Notifications\Notificacion;

class LaravelProgramaController extends Controller
{

    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private NotificacionRepository $notificacionRepo;
    private ActividadRepository $actividadRepo;

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
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
            $this->actividadRepo
            ))
            ->__invoke($procesoID);


        return view('programa_academico.procesos.seguimiento', [
            'seguimiento' => $response->getData()
        ]);            

        // $buscarProceso = new BuscarProcesoUseCase(
        //     FabricaDeRepositorios::getInstance()->getProcesoRepository()
        // );

        // $listaNotificaciones = new ListarNotificacionesPorUsuarioUseCase(
        //     FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        // );

        // $responseBuscarProceso = $buscarProceso->ejecutar($procesoID);
        // if ($responseBuscarProceso->getCode() != 200) {
        //     return redirect()->route('programa_academico.procesos.index')
        //                         ->with($responseBuscarProceso->getCode(), $responseBuscarProceso->getMessage());
        // }

        // $listaNotificacionesResponse = $listaNotificaciones->ejecutar(Auth::user()->email);
        // if ($listaNotificacionesResponse->getCode() != 200) {
        //     return redirect()->route('programa_academico.procesos.index')
        //                         ->with($listaNotificacionesResponse->getCode(), $listaNotificacionesResponse->getMessage());
        // }

        // /** @var \Src\admisiones\domain\Proceso $proceso */
        // $proceso = $responseBuscarProceso->getData();
        

        // return view('programa_academico.procesos.seguimiento', [
        //     'proceso' => $proceso,
        //     'notificaciones' => $listaNotificacionesResponse->getData(),        
        // ]);
    }     
}
