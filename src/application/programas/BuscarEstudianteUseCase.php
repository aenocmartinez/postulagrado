<?php

namespace Src\application\programas;

use Src\domain\repositories\EstudianteRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarEstudianteUseCase
{
    public function __construct(
        private EstudianteRepository $estudianteRepo,
    ) {}
    
    public function ejecutar(string $documentoOrCodigo): ResponsePostulaGrado 
    {

        $estudiantes = $this->estudianteRepo->buscarEstudiantePorCodigo($documentoOrCodigo);

        return new ResponsePostulaGrado(200, "Estudiantes retirado exitosamente.", $estudiantes);
    }
}