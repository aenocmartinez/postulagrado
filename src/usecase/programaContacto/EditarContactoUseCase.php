<?php

namespace Src\usecase\programaContacto;

use Src\domain\ProgramaContacto;
use Src\repositories\ProgramaContactoRepository;
use Src\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class EditarContactoUseCase
{
    public function __construct(
        private ProgramaContactoRepository $programaContactoRepo,
        private ProgramaRepository $programaRepo,
    ) {}

    public function ejecutar(int $contactoID): ResponsePostulaGrado {

        $contacto = $this->programaContactoRepo->buscarPorID($contactoID);
        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(404, "Contacto no encontrado encontrado");
        }

        $programas = $this->programaRepo->listarProgramas();

        $data['contacto'] = $contacto;
        $data['programas'] = $programas;


        return new ResponsePostulaGrado(200, "Contacto encontrado", $data);
    }
}