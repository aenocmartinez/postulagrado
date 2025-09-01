<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEstudianteDatosRequest;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\infrastructure\controller\programa\estudiante\FormularioActualizacionDatosController;
use Src\infrastructure\controller\programa\estudiante\GuardarDatosEstudianteController;
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

        return response()->view('estudiantes.respuesta-actualizacion', [
            'estado'  => $response->getCode(),
            'mensaje' => $response->getMessage(),
        ], $response->getCode());

    }

    public function gurdarDatosEstudiante(UpdateEstudianteDatosRequest $req)
    {
        $validados = $req->validated();
        $datos = ActualizacionDatosDTO::desdeArray($validados);
        
        $response = (new GuardarDatosEstudianteController(
            FabricaDeRepositoriosOracle::getInstance()->getEstudianteRepository()
        ))->__invoke($datos);

        
        return view('estudiantes.respuesta-actualizacion', [
            'code'    => $response->getCode(),
            'message' => $response->getMessage(),
        ]);

    }
}
