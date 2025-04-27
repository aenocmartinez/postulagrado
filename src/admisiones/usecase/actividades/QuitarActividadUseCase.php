<?php

namespace Src\admisiones\usecase\actividades;

use Src\admisiones\repositories\ActividadRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarActividadUseCase
{
    private ProcesoRepository $procesoRepo;
    private ActividadRepository $actividadRepo;

    public function __construct(ProcesoRepository $procesoRepo, ActividadRepository $calendarioRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->actividadRepo = $calendarioRepo;
    }

    public function ejecutar(int $procesoID, int $actividadID): ResponsePostulaGrado 
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");           
        }

        $actividad = $this->actividadRepo->buscarActividadPorId($actividadID);
        if (!$actividad->existe()) 
        {
            return new ResponsePostulaGrado(404, "Actividad no encontrada");  
        }

        $exito = $proceso->quitarActividad($actividad);
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(201, "La actividad se ha eliminado exitosamente.");
    }
}