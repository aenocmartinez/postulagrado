<?php

namespace Src\admisiones\usecase\programas;

use Src\admisiones\repositories\EstudianteRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarEstudianteUseCase
{
    public function __construct(
        private EstudianteRepository $estudianteRepo,
    ) {}
    
    public function ejecutar(string $documentoOrCodigo): ResponsePostulaGrado 
    {

        $estududiantes = $this->estudianteRepo->buscarEstudiantePorCodigo($documentoOrCodigo);

        return new ResponsePostulaGrado(200, "Estudiantes retirado exitosamente.", $estududiantes);
    }
}