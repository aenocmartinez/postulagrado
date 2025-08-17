<?php

namespace Src\infrastructure\controller\proceso\actividad;

use Src\application\procesos\actividades\CrearActividadUseCase;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearActividadController
{
    public function __construct(
        private ActividadRepository $actividadRepo, 
        private ProcesoRepository $procesoRepo
    ){}

    public function __invoke(array $actividades = [], int $procesoID): ResponsePostulaGrado
    {
        return (new CrearActividadUseCase(
                $this->procesoRepo,
                $this->actividadRepo
            ))->ejecutar($procesoID, $actividades);
    }
}