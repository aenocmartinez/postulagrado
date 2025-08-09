<?php

namespace Src\usecase\programas;

use Src\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarProgramasUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo
    ) {}

    public function ejecutar(): ResponsePostulaGrado {

        $programas = $this->programaRepo->listarProgramas();
        return new ResponsePostulaGrado(200, "Programas obtenidos exitosamente.", $programas);
    }
}