<?php

namespace Src\infrastructure\controller\proceso;

use Src\application\procesos\CrearProcesoUseCase;
use Src\application\procesos\DTO\CrearProcesoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearProcesoController 
{    
    public function __construct
    (
        private ProcesoRepository $procesoRepo,
        private NivelEducativoRepository $nivelEducativoRepo,
        private ProgramaRepository $programaRepo
    ){}

    public function __invoke(CrearProcesoDTO $crearProcesoDTO): ResponsePostulaGrado    
    {        
        return (new CrearProcesoUseCase($this->procesoRepo, $this->nivelEducativoRepo, $this->programaRepo))->ejecutar($crearProcesoDTO);
    }       
}