<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\QuitarEstudianteDeProcesoUseCase;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarEstudianteController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private ProgramaRepository $programaRepository
    ) {}

    public function __invoke(int $estudianteProcesoProgramaID): ResponsePostulaGrado
    {
        return (new QuitarEstudianteDeProcesoUseCase(
            $this->programaRepository,
            $this->procesoRepository
        ))->ejecutar($estudianteProcesoProgramaID);
    }
}