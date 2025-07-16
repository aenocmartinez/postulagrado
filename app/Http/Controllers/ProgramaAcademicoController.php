<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\admisiones\usecase\procesos\ListarProcesosUseCase;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaAcademicoController extends Controller
{
    
    public function index()
    {               
        $listaProcesos = new ListarProcesosUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listaProcesos->ejecutar();

        return view('programa_academico.dashboard.index', [
            'procesos' => $response->getData(),
        ]);
    }
}
