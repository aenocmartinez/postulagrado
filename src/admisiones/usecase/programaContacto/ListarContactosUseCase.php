<?php

namespace Src\admisiones\usecase\programaContacto;

use Src\admisiones\repositories\ProgramaContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarContactosUseCase {

    public function __construct(
        private ProgramaContactoRepository $programaContactoRepo
    ) {}

    public function ejecutar(string $criterio = ""): ResponsePostulaGrado {

        $conctactos = $this->programaContactoRepo->listar($criterio);

        return new ResponsePostulaGrado(200, "Registros de contactos encontrados", $conctactos);
    }
}