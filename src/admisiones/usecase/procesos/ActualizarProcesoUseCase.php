<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\dao\mysql\ProcesoDao;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarProcesoUseCase
{

    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(int $id, $datos): ResponsePostulaGrado 
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($id);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $proceso->setNombre($datos['nombre']);
        $proceso->setNivelEducativo($datos['nivelEducativo']);
        $proceso->setEstado($datos['estado']);

        $exito = $proceso->actualizar();
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(200, "Proceso actualizado exitosamente.", $proceso);
    }
}