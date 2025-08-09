<?php

namespace Src\usecase\procesos;

use Illuminate\Support\Facades\Log;
use Src\dto\proceso\ProcesoDTO;
use Src\repositories\NivelEducativoRepository;
use Src\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarProcesoUseCase
{
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelRepo;

    public function __construct(ProcesoRepository $procesoRepo, NivelEducativoRepository $nivelRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->nivelRepo = $nivelRepo;
    }

    public function ejecutar(int $id, ProcesoDTO $procesoDTO): ResponsePostulaGrado 
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($id);
        if (!$proceso->existe()) 
        {
            Log::error("Proceso no encontrado con ID: $id");
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $nivelEducativo = $this->nivelRepo->BuscarPorID($procesoDTO->getNivelEducativo()->getId());
        if (!$nivelEducativo->existe()) 
        {
            return new ResponsePostulaGrado(404, "Nivel educativo no encontrado.");
        }        

        $proceso->setNombre($procesoDTO->getNombre());
        $proceso->setNivelEducativo($nivelEducativo);
        // $proceso->setEstado($datos['estado']);

        $exito = $proceso->actualizar();
        if (!$exito) 
        {
            Log::error("Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(200, "Proceso actualizado exitosamente.", $proceso);
    }
}