<?php

namespace Src\admisiones\procesos\usecase;

use Src\admisiones\procesos\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class EditarProcesoUseCase
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

        $response->setCode(200);
        $response->setData($proceso);
        return $response;
    }
}