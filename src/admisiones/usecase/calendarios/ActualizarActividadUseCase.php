<?php

namespace Src\admisiones\usecase\calendarios;

use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarActividadUseCase
{

    private ProcesoRepository $procesoRepo;
    private CalendarioRepository $calendarioRepo;

    public function __construct(ProcesoRepository $procesoRepo, CalendarioRepository $calendarioRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->calendarioRepo = $calendarioRepo;
    }

    public function ejecutar(int $procesoID, $datos): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $actividad = $this->calendarioRepo->buscarActividadPorId($datos['actividad_id']);
        if (!$actividad->existe()) 
        {
            return new ResponsePostulaGrado(404, "Actividad no encontrada");
        }

        $actividad->setDescripcion($datos['descripcion']);
        $actividad->setFechaInicio($datos['fecha_inicio']);
        $actividad->setFechaFin($datos['fecha_fin']);

        $exito = $proceso->actualizarActividad($actividad);
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(200, "La actividad se ha actualizado exitosamente.");
    }
}