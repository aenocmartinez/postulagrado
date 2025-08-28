<?php

namespace Src\infrastructure\controller\programa\estudiante;

use Src\application\programas\estudiante\FormularioActualizacionDatosEstudianteUseCase;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class FormularioActualizacionDatosController
{

    public function __construct(
        private ProgramaRepository $programaRepo,
        private EnlaceActualizacionRepository $enlaceRepo
    ) {}

    public function __invoke(string $token): ResponsePostulaGrado
    {
        return (new FormularioActualizacionDatosEstudianteUseCase(
            $this->enlaceRepo,
            $this->programaRepo
            ))->ejecutar($token);

    }
}