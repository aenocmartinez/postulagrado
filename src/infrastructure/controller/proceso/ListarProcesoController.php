<?php

namespace Src\Infrastructure\Controller\Proceso;

use Src\application\procesos\ListarProcesosUseCase;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarProcesoController
{
    private ProcesoRepository $procesoRepository;
    private NivelEducativoRepository $nivelEducativoRepository;

    public function __construct(ProcesoRepository $procesoRepository, NivelEducativoRepository $nivelEducativoRepository)
    {
        $this->procesoRepository = $procesoRepository;
        $this->nivelEducativoRepository = $nivelEducativoRepository;
    }

    public function __invoke(): ResponsePostulaGrado
    {
        $listarProcesosUseCase = new ListarProcesosUseCase($this->procesoRepository, $this->nivelEducativoRepository);

        return $listarProcesosUseCase->ejecutar();         
    }
}