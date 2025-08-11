<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Src\shared\di\FabricaDeRepositorios;
use Src\usecase\enlaceActualizacion\ObtenerDatosFormularioActualizacionUseCase;

class ActualizacionDatosController extends Controller
{
   
    public function mostrarFormularioActualizacion(string $token)
    {
        $obtenerDatosFormulario = new ObtenerDatosFormularioActualizacionUseCase(
            FabricaDeRepositorios::getInstance()->getEnlaceActualizacionRepository(),
            FabricaDeRepositorios::getInstance()->getProgramaRepository(),
        );
        $resp = $obtenerDatosFormulario->ejecutar($token);

        
        if ($resp->getCode() === 200) {
            $data = $resp->getData() ?? [];
            return view('estudiantes.form-actualizacion', $data);
        }

        // Puedes usar vistas específicas de estado, o abortar con código/mensaje
        // 404 = no encontrado, 410 = expirado, 409 = ya usado, etc.
        return response()->view('estudiantes.form-actualizacion', [
            'estado'  => $resp->getCode(),
            'mensaje' => $resp->getMessage(),
        ], $resp->getCode());
    }
}
