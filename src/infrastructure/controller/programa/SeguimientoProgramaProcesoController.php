<?php

namespace Src\infrastructure\controller\programa;

use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;

class SeguimientoProgramaProcesoController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private NivelEducativoRepository $nivelEducativoRepository
    ) {}

    public function __invoke(int $procesoID)
    {

    }
}