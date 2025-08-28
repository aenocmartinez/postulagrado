<?php

namespace Src\infrastructure\controller\programa;

use Src\application\programas\BuscarEstudianteUseCase;
use Src\domain\repositories\EstudianteRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarEstudianteController 
{
    public function __construct(
        private EstudianteRepository $estudianteRepo
    ) {}

    public function __invoke(string $documentoOrCodigo): ResponsePostulaGrado
    {
        return (new BuscarEstudianteUseCase($this->estudianteRepo))->ejecutar($documentoOrCodigo);
    }  
}