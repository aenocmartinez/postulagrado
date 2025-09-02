<?php

namespace Src\infrastructure\controller\programa\estudiante;

use Illuminate\Support\Facades\Auth;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\application\programas\estudiante\ActualizarDatosEstudianteUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class GuardarDatosEstudianteController
{
    
    public function __construct(
        private EstudianteRepository $estudianteRepo,
        private ContactoRepository $contactoRepo,
        private NotificacionRepository $notificacionRepo,
    ){}

    public function __invoke(ActualizacionDatosDTO $datos): ResponsePostulaGrado
    {
        return (new ActualizarDatosEstudianteUseCase(
            $this->estudianteRepo,
            $this->contactoRepo,
            $this->notificacionRepo,
        ))->ejecutar($datos);
    }
}