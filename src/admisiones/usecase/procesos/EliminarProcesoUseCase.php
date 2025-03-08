<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EliminarProcesoUseCase
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

        $tieneCalendarioConActividades = $this->procesoRepo->tieneCalendarioConActividades($proceso->getId());
        if ($tieneCalendarioConActividades) 
        {
            return new ResponsePostulaGrado(409, "No es posible eliminar el proceso, ya que está vinculado a un calendario.");
        }

        $exito = $proceso->eliminar();
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }        

        return new ResponsePostulaGrado(200, "El proceso se ha eliminado exitosamente.");
    }
}