<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEstudianteDatosRequest;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\domain\programa\contacto\Contacto;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\infrastructure\controller\programa\estudiante\FormularioActualizacionDatosController;
use Src\infrastructure\controller\programa\estudiante\GuardarDatosEstudianteController;
use Src\shared\di\FabricaDeRepositoriosOracle;

class LaravelActualizacionDatosController extends Controller
{
    private ProgramaRepository $programaRepo;
    private EnlaceActualizacionRepository $enlaceRepo;
    private ContactoRepository $contactoRepo;
    private EstudianteRepository $estudianteRepo;
    private NotificacionRepository $notificacionRepo;

    public function __construct()
    {
        $this->programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
        $this->enlaceRepo = FabricaDeRepositoriosOracle::getInstance()->getEnlaceActualizacionRepository();
        $this->contactoRepo = FabricaDeRepositoriosOracle::getInstance()->getContactoRepository();
        $this->estudianteRepo = FabricaDeRepositoriosOracle::getInstance()->getEstudianteRepository();
        $this->notificacionRepo = FabricaDeRepositoriosOracle::getInstance()->getNotificacionRepository();
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
            $this->estudianteRepo,
            $this->contactoRepo,
            $this->notificacionRepo
        ))->__invoke($datos);
        
        return view('estudiantes.respuesta-actualizacion', [
            'code'    => $response->getCode(),
            'message' => $response->getMessage(),
        ]);

    }
}
