<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class EliminarProcesoUseCase
{
    public static function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        $response = new ResponsePostulaGrado();

        $proceso = ProcesoDao::buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            $response->setCode(404);
            $response->setMessage('Proceso no encontrado');
            return $response;            
        }

        $tieneCalendarioConActividades = ProcesoDao::tieneCalendarioConActividades($proceso->getId());
        if ($tieneCalendarioConActividades) {
            $response->setCode(409);
            $response->setMessage('No es posible eliminar el proceso, ya que está vinculado a un calendario.');
            return $response;                        
        }

        $exito = $proceso->eliminar();
        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }        

        $response->setCode(200);
        $response->setMessage('El proceso se ha eliminado exitosamente.');
        return $response;
    }
}