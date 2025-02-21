<?php

namespace Src\admisiones\usecase\calendarios;

use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarActividadUseCase
{

    public static function ejecutar(int $procesoID, $datos): ResponsePostulaGrado
    {
        $response = new ResponsePostulaGrado();

        $proceso = ProcesoDao::buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            $response->setCode(404);
            $response->setMessage('Proceso no encontrado');
            return $response;                                    
        }

        $actividad = CalendarioDao::buscarActividadPorId($datos['actividad_id']);
        if (!$actividad->existe()) {
            $response->setCode(404);
            $response->setMessage('Actividad no encontrada');
            return $response;                                    
        }

        $actividad->setDescripcion($datos['descripcion']);
        $actividad->setFechaInicio($datos['fecha_inicio']);
        $actividad->setFechaFin($datos['fecha_fin']);

        $exito = $proceso->actualizarActividad($actividad);
        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }

        $response->setCode(200);
        $response->setMessage('La actividad se ha actualizado exitosamente.');
        return $response;
    }
}