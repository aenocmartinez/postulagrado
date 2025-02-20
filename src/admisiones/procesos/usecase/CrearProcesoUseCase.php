<?php

namespace Src\admisiones\procesos\usecase;

use App\Http\Requests\CrearProceso;
use Src\admisiones\procesos\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class CrearProcesoUseCase
{
    public static function ejecutar($datos): ResponsePostulaGrado
    {
        $response = new ResponsePostulaGrado();

        $proceso = ProcesoDao::buscarProcesoPorNombre($datos['nombre']);
        if ($proceso->existe()) {
            $response->setCode(409);
            $response->setMessage('El nombre del proceso ya está en uso. Por favor, elige un nombre diferente.');
            return $response;
        }

        $proceso->setNombre($datos['nombre']);
        $proceso->setNivelEducativo($datos['nivelEducativo']);
        $proceso->setEstado('Abierto');

        $exito = $proceso->crear();

        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }


        $response->setCode(200);
        $response->setMessage('El proceso se ha creado exitosamente.');
        return $response;
    }
}