<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\infrastructure\controller\proceso\actividad\CrearActividadController;
use Src\infrastructure\controller\proceso\actividad\ListarActividadController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelActividadController extends Controller
{
    private ProcesoRepository $procesoRepo;
    private ActividadRepository $actividadRepo;
    private NivelEducativoRepository $nivelEducativoRepo;

    public function __construct()
    {
        $this->procesoRepo =  FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
        $this->actividadRepo = FabricaDeRepositoriosOracle::getInstance()->getActividadRepository();
        $this->nivelEducativoRepo = FabricaDeRepositoriosOracle::getInstance()->getNivelEducativoRepository();

    }

    public function index($procesoID)
    {
        $response = (new ListarActividadController(
                        $this->procesoRepo, 
                        $this->actividadRepo, 
                        $this->nivelEducativoRepo))($procesoID);

        if ($response->getCode() != 200) {
            return redirect()->route('procesos.index')->with($response->getCode(), $response->getMessage());
        }

        return view('admisiones.procesos.actividades.index', [
            'procesoActividad' => $response->getData(),
        ]);
    }

    public function store(Request $request, int $procesoID)
    {
        $actividades = json_decode($request->input('actividades'), true);
        $response = (new CrearActividadController(
                        $this->actividadRepo,
                        $this->procesoRepo          
                    ))($actividades, $procesoID);

        return redirect()->route('procesos.actividades', $procesoID)
                         ->with($response->getCode(), $response->getMessage());
    }

}
