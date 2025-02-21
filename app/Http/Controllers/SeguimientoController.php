<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\admisiones\usecase\procesos\BuscarProcesoUseCase;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;

class SeguimientoController extends Controller
{
    public function index()
    {
        $procesos = ListarProcesosUseCase::ejecutar();
        return view('seguimientos.index', [
            'procesos' => $procesos
        ]);
    }

    public function show($procesoID)
    {
        $response = BuscarProcesoUseCase::ejecutar($procesoID);
        if ($response->getCode() != 200) {
            return redirect()->route('seguimientos.index')->with($response->getCode(), $response->getMessage());
        }

        return view('seguimientos.show', [
            'proceso' => $response->getData()
        ]);
    }
}
