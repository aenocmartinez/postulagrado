<?php

namespace Src\infrastructure\controller\seguimiento;

use Src\application\procesos\ListarProcesosUseCase;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class SeguimientoIndexController
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NivelEducativoRepository $nivelEducativoRepo
    ) {}

    public function __invoke(): ResponsePostulaGrado
    {
       return (new ListarProcesosUseCase($this->procesoRepo, $this->nivelEducativoRepo))->ejecutar();

    }
}