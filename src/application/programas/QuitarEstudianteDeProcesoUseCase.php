<?php

namespace Src\usecase\programas;

use Src\repositories\ProcesoRepository;
use Src\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class QuitarEstudianteDeProcesoUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo,
        private ProcesoRepository $procesoRepo,
    ) {}
    
    public function ejecutar(int $estudianteProcesoProgramaID): ResponsePostulaGrado 
    {

        $this->programaRepo->quitarEstudiante($estudianteProcesoProgramaID);

        return new ResponsePostulaGrado(200, "Estudiantes retirado exitosamente.");
    }
}