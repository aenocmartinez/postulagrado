<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEstudianteDatosRequest;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\infrastructure\controller\programa\estudiante\FormularioActualizacionDatosController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelActualizacionDatosController extends Controller
{
    private ProgramaRepository $programaRepo;
    private EnlaceActualizacionRepository $enlaceRepo;

    public function __construct()
    {
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
        $this->enlaceRepo = FabricaDeRepositoriosOracle::getInstance()->getEnlaceActualizacionRepository();
    }
   
    public function mostrarFormularioActualizacion(string $token)
    {
       $response = (new FormularioActualizacionDatosController(
            $this->programaRepo,
            $this->enlaceRepo,
        ))->__invoke($token);
        
        if ($response->getCode() === 200) {
            $data = $response->getData() ?? [];
            return view('estudiantes.form-actualizacion', $data);
        }

        // Puedes usar vistas específicas de estado, o abortar con código/mensaje
        // 404 = no encontrado, 410 = expirado, 409 = ya usado, etc.
        return response()->view('estudiantes.form-actualizacion', [
            'estado'  => $response->getCode(),
            'mensaje' => $response->getMessage(),
        ], $response->getCode());
    }

    public function gurdarDatosEstudiante(UpdateEstudianteDatosRequest $req)
    {
        $req = $req->validated();

        dd($req);
    }
}
