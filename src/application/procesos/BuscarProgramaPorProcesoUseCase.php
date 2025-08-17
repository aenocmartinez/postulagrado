<?php

namespace Src\application\usecase\procesos;

use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarProgramaPorProcesoUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ProgramaRepository $programaRepo
    ) {}

    public function ejecutar(int $procesoID, int $programaID): ResponsePostulaGrado {

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($proceso->getId(), $programaID);
        if (!$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        $programaProceso->setProceso($proceso);

        return new ResponsePostulaGrado(200, "Programa encontrado", $programaProceso);
    }
}