<?php

namespace Src\usecase\procesos;

use Src\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarProcesosUseCase
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(): ResponsePostulaGrado
    {
        $procesos = $this->procesoRepo->listarProcesos();
        
        return new ResponsePostulaGrado(200, "Procesos obtenidos exitosamente.", $procesos);
    }
}