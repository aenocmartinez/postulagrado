<?php

namespace Src\usecase\programas;

use Src\repositories\EstudianteRepository;
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