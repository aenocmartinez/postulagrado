<?php

namespace Src\infrastructure\controller\proceso;

use Src\application\procesos\QuitarProgramaAProcesoUseCase;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarProgramaAProcesoController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private ProgramaRepository $programaRepository
    ) {}

    public function __invoke(int $procesoID, int $programaID): ResponsePostulaGrado
    {
        return (new QuitarProgramaAProcesoUseCase(
            $this->procesoRepository,
            $this->programaRepository
        ))->ejecutar($procesoID, $programaID);
    }
}