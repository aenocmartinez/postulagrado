<?php

namespace Src\application\programas;

use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarEstudiantesCandidatosGradoUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo
    ) {}


    public function ejecutar(int $codigoPrograma, int $anio, int $periodo): ResponsePostulaGrado 
    {
        $estudiantes = $this->programaRepo->buscarEstudiantesCandidatosAGrado($codigoPrograma, $anio, $periodo);

        $estudiantesArray = array_map(fn($e) => $e->toArray(), $estudiantes);

        return new ResponsePostulaGrado(200, "Programa encontrado exitosamente.", $estudiantesArray);
    }
}