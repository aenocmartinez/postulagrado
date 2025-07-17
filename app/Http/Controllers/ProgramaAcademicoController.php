<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\admisiones\usecase\notificaciones\ListarNotificacionesPorUsuarioUseCase;
use Src\admisiones\usecase\procesos\BuscarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
use Src\admisiones\usecase\programas\BuscarEstudiantesCandidatosGradoUseCase;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaAcademicoController extends Controller
{
    
    public function dashboard()
    {               
        return view('programa_academico.dashboard.index');
    }

    public function procesos()
    {
        $listaProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listaProcesos->ejecutar();

        return view('programa_academico.procesos.index', [
            'procesos' => $response->getData(),
        ]);
    }

    public function seguimientoProceso($procesoID)
    {

        $buscarProceso = new BuscarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $listaNotificaciones = new ListarNotificacionesPorUsuarioUseCase(
            FabricaDeRepositorios::getInstance()->getNotificacionRepository()
        );

        $responseBuscarProceso = $buscarProceso->ejecutar($procesoID);
        if ($responseBuscarProceso->getCode() != 200) {
            return redirect()->route('programa_academico.procesos.index')
                                ->with($responseBuscarProceso->getCode(), $responseBuscarProceso->getMessage());
        }

        $listaNotificacionesResponse = $listaNotificaciones->ejecutar(Auth::user()->email);
        if ($listaNotificacionesResponse->getCode() != 200) {
            return redirect()->route('programa_academico.procesos.index')
                                ->with($listaNotificacionesResponse->getCode(), $listaNotificacionesResponse->getMessage());
        }

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $responseBuscarProceso->getData();
        

        return view('programa_academico.procesos.seguimiento', [
            'proceso' => $proceso,
            'notificaciones' => $listaNotificacionesResponse->getData(),        
        ]);
    }    

    public function buscarEstudiantesCandidatosAGrado(int $codigoPrograma, int $anio, int $periodo)
    {
    
        $buscarCantidatosGrado = new BuscarEstudiantesCandidatosGradoUseCase(
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        );

        $response = $buscarCantidatosGrado->ejecutar(
            Auth::user()->programaAcademico()->getCodigo(), 
            $anio, 
            $periodo);
            

        return response()->json([
                'code' => $response->getCode(),
                'message' => $response->getMessage(),
                'data' => $response->getData()
            ], $response->getCode());

    }
}
