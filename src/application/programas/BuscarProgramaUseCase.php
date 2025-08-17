<?php

namespace Src\usecase\programas;

use Src\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarProgramaUseCase
{
    public function __construct(
        private ProgramaRepository $programaRepo
    ) {}

    public function ejecutar(int $id): ResponsePostulaGrado {

        $programa = $this->programaRepo->buscarPorID($id);

        if (!$programa->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado.");
        }

        return new ResponsePostulaGrado(200, "Programa encontrado exitosamente.", $programa);
    }
}