<?php

namespace Src\application\procesos;

use Src\domain\repositories\ProcesoRepository;
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

        $tieneActividades = $this->procesoRepo->tieneActividades($proceso->getId());
        if ($tieneActividades) 
        {
            return new ResponsePostulaGrado(409, "No es posible eliminar el proceso, ya que tiene actividades relacionadas.");
        }

        $exito = $this->procesoRepo->quitarTodosLosPrograma($proceso->getId());
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error al eliminar los programas asociados al proceso.");
        }

        $exito = $this->procesoRepo->eliminarProceso($proceso->getId());
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }        

        return new ResponsePostulaGrado(200, "El proceso se ha eliminado exitosamente.");
    }
}