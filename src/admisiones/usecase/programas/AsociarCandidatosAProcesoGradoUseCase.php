<?php

namespace Src\admisiones\usecase\programas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Src\admisiones\repositories\ProcesoRepository;
use Src\admisiones\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class AsociarCandidatosAProcesoGradoUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo,
    ) {}
    
    public function ejecutar(int $procesoID, $estudiantes=[], int $anio, int $periodo): ResponsePostulaGrado 
    {

        $programa = Auth::user()->programaAcademico();

        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($procesoID, $programa->getId());

        if (!$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, "Programa no asociado al proceso.");
        }       
        
        foreach($estudiantes as $codigoEstudiante) {

            $exito = $this->procesoRepo->agregarCandidatoAProceso($programaProceso->getId(), $codigoEstudiante, $anio, $periodo);

            if (!$exito) {
                Log::info("Este codigo de estudiante no encontrado: ", $codigoEstudiante);
                continue;
            }

        }

        return new ResponsePostulaGrado(200, "Estudiantes asociados exitosamente.");
    }
}