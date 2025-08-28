<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\BuscarEstudiantesCandidatosGradoUseCase;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarCandidatosController
{
    public function __construct(
        private ProgramaRepository $programaRepo        
    ) {}

    public function __invoke(int $codigoPrograma, int $anio, int $periodo): ResponsePostulaGrado
    {
        return (new BuscarEstudiantesCandidatosGradoUseCase($this->programaRepo))
                ->ejecutar($codigoPrograma, $anio, $periodo);
    }
}