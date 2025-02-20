<?php

namespace Src\admisiones\procesos\usecase;

use Src\admisiones\procesos\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarProcesoUseCase
{
    public static function ejecutar($datos): ResponsePostulaGrado {

        $response = new ResponsePostulaGrado();

        $proceso = ProcesoDao::buscarProcesoPorId($datos['id']);
        if (!$proceso->existe()) {
            $response->setCode(404);
            $response->setMessage('Proceso no encontrado');
            return $response;                        
        }

        $proceso->setNombre($datos['nombre']);
        $proceso->setNivelEducativo($datos['nivelEducativo']);
        $proceso->setEstado($datos['estado']);

        $exito = $proceso->actualizar();
        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }

        $response->setCode(200);
        $response->setMessage('El proceso se ha actualizado exitosamente.');
        return $response;
    }
}