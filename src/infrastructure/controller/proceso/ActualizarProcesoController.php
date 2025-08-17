<?php

namespace Src\infrastructure\controller\proceso;

use Src\application\procesos\ActualizarProcesoUseCase;
use Src\Application\procesos\DTO\EditarProcesoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarProcesoController
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NivelEducativoRepository $nivelEducativoRepo,
    ){}

    public function __invoke(EditarProcesoDTO $editarProcesoDTO): ResponsePostulaGrado
    {
        try
        {
            $procesoID = filter_var($editarProcesoDTO->id, FILTER_VALIDATE_INT);
            if ($procesoID === false) 
            {
                return new ResponsePostulaGrado(500, 'ID de proceso inválido.');
            }
            
            return (new ActualizarProcesoUseCase($this->procesoRepo, $this->nivelEducativoRepo))->ejecutar($editarProcesoDTO);
        }
        catch (\Throwable $e) {
            return new ResponsePostulaGrado(500, $e->getMessage() ?: 'Ha ocurrido un error interno.');
        }
    }
}