<?php

namespace Src\admisiones\usecase\procesos;

use DragonCode\Contracts\Cashier\Http\Response;
use Illuminate\Routing\Events\ResponsePrepared;
use Src\admisiones\repositories\ProcesoRepository;
use Src\admisiones\repositories\ProgramaRepository;
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

        $programaProceso = $proceso->getPrograma($programaID);

        if (!$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        return new ResponsePostulaGrado(200, "Programa encontrado", $programaProceso);
    }
}