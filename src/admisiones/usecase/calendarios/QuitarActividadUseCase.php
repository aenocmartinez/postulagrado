<?php

namespace Src\admisiones\usecase\calendarios;

use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarActividadUseCase
{
    private ProcesoRepository $procesoRepo;
    private CalendarioRepository $calendarioRepo;

    public function __construct(ProcesoRepository $procesoRepo, CalendarioRepository $calendarioRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->calendarioRepo = $calendarioRepo;
    }

    public function ejecutar(int $procesoID, int $actividadID): ResponsePostulaGrado 
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");           
        }

        $actividad = $this->calendarioRepo->buscarActividadPorId($actividadID);
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