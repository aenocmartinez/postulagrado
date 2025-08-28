<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\ObtenerDetalleEstudianteProcesoUseCase;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;


class ObtenerDetalleEstudianteController
{
    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo,
        private EstudianteRepository $estudianteRepo
    ) {}

    public function __invoke(int $procesoId, string $codigo): ResponsePostulaGrado
    {
        return (new ObtenerDetalleEstudianteProcesoUseCase(
            $this->programaRepo,
            $this->procesoRepo,
            $this->estudianteRepo
        ))->ejecutar($procesoId, $codigo);
    }
}