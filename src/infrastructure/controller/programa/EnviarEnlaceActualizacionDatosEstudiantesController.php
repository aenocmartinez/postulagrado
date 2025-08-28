<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\EnviarEnlaceActualizacionUseCase;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class EnviarEnlaceActualizacionDatosEstudiantesController
{
    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo,
        private EnlaceActualizacionRepository $enlaceActualizacionRepo
    ) {}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        return (new EnviarEnlaceActualizacionUseCase(
            $this->programaRepo,
            $this->procesoRepo,
            $this->enlaceActualizacionRepo
        ))->ejecutar($procesoID);
    }
}