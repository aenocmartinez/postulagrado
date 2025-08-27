<?php

namespace App\Http\Controllers;

use Src\domain\NivelEducativo;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\Infrastructure\Controller\Proceso\ListarProcesoController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelProgramaController extends Controller
{

    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepo;

    public function __construct()
    {
        $this->procesoRepo = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();
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
