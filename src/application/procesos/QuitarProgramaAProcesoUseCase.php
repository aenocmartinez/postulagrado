<?php

namespace Src\application\procesos;

use Illuminate\Support\Facades\Log;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarProgramaAProcesoUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ProgramaRepository $programaRepo
    ){}

    public function ejecutar(int $procesoID, int $programaID): ResponsePostulaGrado {

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            Log::error("Proceso no encontrado");
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $programa = $this->programaRepo->buscarPorID($programaID);
        if (!$programa->existe()) {
            Log::error("Programa no encontrado");
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        $exito = $this->procesoRepo->quitarPrograma($proceso->getId(), $programa->getId());
        if (!$exito) {
            Log::error("Ha ocurrido un error en el sistema al quitar el programa del proceso.");
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema al quitar el programa del proceso.");
        }

        return new ResponsePostulaGrado(200, "Programa retirado del proceso con éxito");
    }
}