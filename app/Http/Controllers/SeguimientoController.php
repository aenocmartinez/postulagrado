<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\admisiones\usecase\procesos\BuscarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class SeguimientoController extends Controller
{
    public function index()
    {
        $listaProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listaProcesos->ejecutar();

        return view('seguimientos.index', [
            'procesos' => $response->getData()
        ]);
    }

    public function show($procesoID)
    {
        $buscarProceso = new BuscarProcesoUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $buscarProceso->ejecutar($procesoID);
        if ($response->getCode() != 200) {
            return redirect()->route('seguimientos.index')->with($response->getCode(), $response->getMessage());
        }

        return view('seguimientos.show', [
            'proceso' => $response->getData()
        ]);
    }
}
