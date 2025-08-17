<?php

namespace Src\application\usecase\actividades;

use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class AgregarActividadUseCase 
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(int $procesoID, $datos): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $exito = $proceso->agregarActividad($datos['descripcion'], $datos['fecha_inicio'], $datos['fecha_fin']);
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(201, "La actividad se ha creado exitosamente.");
    }
}