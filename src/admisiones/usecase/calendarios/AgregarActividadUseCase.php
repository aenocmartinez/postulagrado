<?php

namespace Src\admisiones\usecase\calendarios;

use Src\admisiones\dao\mysql\ProcesoDao;
use Src\shared\response\ResponsePostulaGrado;

class AgregarActividadUseCase {

    public static function ejecutar(int $procesoID, $datos): ResponsePostulaGrado
    {
        $response = new ResponsePostulaGrado();

        $proceso = ProcesoDao::buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            $response->setCode(404);
            $response->setMessage('Proceso no encontrado');
            return $response;                                    
        }

        $exito = $proceso->agregarActividad($datos['descripcion'], $datos['fecha_inicio'], $datos['fecha_fin']);
        if (!$exito) {
            $response->setCode(500);
            $response->setMessage('Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.');
            return $response;            
        }

        $response->setCode(201);
        $response->setMessage('La actividad se ha creado exitosamente.');
        return $response;
    }
}