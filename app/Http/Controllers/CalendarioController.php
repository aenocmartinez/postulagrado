<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearActividad;
use Illuminate\Http\Request;
use Src\admisiones\usecase\calendarios\ActualizarActividadUseCase;
use Src\admisiones\usecase\calendarios\AgregarActividadUseCase;
use Src\admisiones\usecase\calendarios\ListarActividadesUseCase;
use Src\admisiones\usecase\calendarios\QuitarActividadUseCase;
use Src\shared\di\FabricaDeRepositorios;
use Src\shared\response\ResponsePostulaGrado;

class CalendarioController extends Controller
{
    public function index($procesoID)
    {

        $listarActividades = new ListarActividadesUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        $response = $listarActividades->ejecutar($procesoID);

        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }


        return view('calendarios.index', [
            'proceso' => $response->getData(),
        ]);
    }

    public function store(CrearActividad $request, int $procesoID)
    {
        $datos = $request->validated();

        if (isset(request()->actividad_id) && request()->actividad_id > 0)          
            $response = $this->actualizarActividad($procesoID, $datos);
        else 
            $response = $this->agregarActividad($procesoID, $datos);

        if ($response->getCode() != 201 && 
            $response->getCode() != 200
            ) {

            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        return redirect()->route('procesos.actividades', $procesoID)->with($response->getCode(), $response->getMessage());
    }

    private function actualizarActividad($procesoID, $datos): ResponsePostulaGrado
    {
        $datos['actividad_id'] = request()->actividad_id;

        $actualizarActividad = new ActualizarActividadUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository()                
        );
        
        return $actualizarActividad->ejecutar($procesoID, $datos);
    } 

    private function agregarActividad($procesoID, $datos): ResponsePostulaGrado
    {
        $agregarActividad = new AgregarActividadUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository()
        );

        return $agregarActividad->ejecutar($procesoID, $datos);
    }     

    public function destroy(int $procesoID, int $actividadID)
    {
        $quitarActividad = new QuitarActividadUseCase(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository()                 
        );
        
        $response = $quitarActividad->ejecutar($procesoID, $actividadID);
        
        return redirect()->route('procesos.actividades', $procesoID)->with($response->getCode(), $response->getMessage());
    }
    
}
