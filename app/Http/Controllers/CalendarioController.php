<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearActividad;
use Illuminate\Http\Request;
use Src\admisiones\usecase\calendarios\AgregarActividadUseCase;
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

    public function store(CrearActividad $request, int $procesoID)
    {
        $response = AgregarActividadUseCase::ejecutar($procesoID, $request);
        if ($response->getCode() != 201) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        return redirect()->route('procesos.actividades', $procesoID)
                ->with('status', ['code' => $response->getCode(), 'message' => $response->getMessage()]);

    }
}
