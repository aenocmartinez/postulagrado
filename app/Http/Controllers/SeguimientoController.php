<?php

namespace App\Http\Controllers;

use Src\usecase\procesos\BuscarProcesoUseCase;
use Src\usecase\procesos\ListarProcesosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class SeguimientoController extends Controller
{
    public function index()
    {
        $listaProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listaProcesos->ejecutar();

        return view('admisiones.seguimientos.index', [
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

        return view('admisiones.seguimientos.show', [
            'proceso' => $response->getData()
        ]);
    }
}
