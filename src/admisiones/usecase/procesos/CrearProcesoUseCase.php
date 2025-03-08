<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearProcesoUseCase
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;    
    }

    public function ejecutar($datos): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorNombreYNivelEducativo($datos['nombre'], $datos['nivelEducativo']);
        if ($proceso->existe()) 
        {
            return new ResponsePostulaGrado(409, "El nombre del proceso ya está en uso. Por favor, elige un nombre diferente.");
        }

        $proceso->setNombre($datos['nombre']);
        $proceso->setNivelEducativo($datos['nivelEducativo']);
        $proceso->setEstado('Abierto');

        $exito = $proceso->crear();
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");   
        }

        return new ResponsePostulaGrado(201, "El proceso se ha creado exitosamente.");
    }
}