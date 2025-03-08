<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\dao\mysql\ProcesoDao;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EditarProcesoUseCase
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;    
    }

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {        
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {            
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        return new ResponsePostulaGrado(200, "Proceso obtenido exitosamente", $proceso);
    }
}