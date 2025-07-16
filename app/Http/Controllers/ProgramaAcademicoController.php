<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\admisiones\usecase\procesos\BuscarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
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

        $response = $buscarProceso->ejecutar($procesoID);
        if ($response->getCode() != 200) {
            return redirect()->route('programa_academico.procesos.index')->with($response->getCode(), $response->getMessage());
        }

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $response->getData();   

        // echo "Proceso => " . $procesoID . "<br>";
        // echo "Proceso => " . Auth::user()->programaAcademico()->getId() . "<br>";
        // $buscarProceso = new BuscarProcesoUseCase(
        //     FabricaDeRepositorios::getInstance()->getProcesoRepository()
        // );

        // $response = $buscarProceso->ejecutar($procesoID);
        // if ($response->getCode() != 200) {
        //     return redirect()->route('seguimientos.index')->with($response->getCode(), $response->getMessage());
        // }

        return view('programa_academico.procesos.seguimiento', [
            'proceso' => $proceso,
        
        ]);
    }    
}
