<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\AsociarCandidatosAProcesoGradoUseCase;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;


class AgregarCandidatosController
{
    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo
    ) {}

    public function __invoke(array $estudiantes, int $procesoID, int $anio, int $periodo): ResponsePostulaGrado
    {
        return (new AsociarCandidatosAProcesoGradoUseCase(
            $this->programaRepo,
            $this->procesoRepo
        ))->ejecutar(
            $procesoID, 
            $estudiantes, 
            $anio, 
            $periodo
        );
    }
}