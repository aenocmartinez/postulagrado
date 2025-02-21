<?php

namespace Src\admisiones\usecase\calendarios;

use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class QuitarActividadUseCase
{
    public static function ejecutar(int $procesoID, int $actividadID): ResponsePostulaGrado 
    {
        $response = new ResponsePostulaGrado();
    
        $proceso = ProcesoDao::buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            $response->setCode(404);
            $response->setMessage('Proceso no encontrado');
            return $response;                            
        }

        $actividad = CalendarioDao::buscarActividadPorId($actividadID);
        if (!$actividad->existe()) {
            $response->setCode(404);
            $response->setMessage('Actividad no encontrada');
            return $response;                            
        }

        $exito = $proceso->quitarActividad($actividad);
        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }

        $response->setCode(201);
        $response->setMessage('La actividad se ha eliminado exitosamente.');
        return $response;

        return $response;
    }
}