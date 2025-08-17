<?php

 namespace Src\Infrastructure\Controller\Proceso;

use Src\application\procesos\EliminarProcesoUseCase;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

 class EliminarProcesoController 
 {
    public function __construct(
        private ProcesoRepository $procesoRepo
    ){}

    public function __invoke(string|int $procesoID): ResponsePostulaGrado
    {
        try
        {
            $procesoID = filter_var($procesoID, FILTER_VALIDATE_INT);
            if ($procesoID === false) 
            {
                return new ResponsePostulaGrado(500, 'ID de proceso inválido.');
            }
            
            return (new EliminarProcesoUseCase($this->procesoRepo))->ejecutar($procesoID);
        }
        catch (\Throwable $e) {
            return new ResponsePostulaGrado(500, 'Ha ocurrido un error interno.');
        }
    }
 }