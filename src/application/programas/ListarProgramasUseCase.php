<?php

namespace Src\application\programas;

use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarProgramasUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo
    ) {}

    public function ejecutar(): array {

        return  $this->programaRepo->listarProgramas();
    }
}