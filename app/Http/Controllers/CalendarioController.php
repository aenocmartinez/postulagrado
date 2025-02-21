<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\admisiones\usecase\calendarios\ListarActividadesUseCase;

class CalendarioController extends Controller
{
    public function index($procesoID)
    {
        $response = ListarActividadesUseCase::ejecutar($procesoID);

        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }


        return view('calendarios.index', [
            'proceso' => $response->getData(),
        ]);
    }
}
