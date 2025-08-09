<?php

namespace Src\usecase\programaContacto;

use Src\repositories\ProgramaContactoRepository;
use Src\shared\di\FabricaDeRepositorios;
use Src\shared\response\ResponsePostulaGrado;

class EliminarContactoUseCase {

    public function __construct(
        private ProgramaContactoRepository $repository
    ){}

    public function ejecutar(int $programaContactoID): ResponsePostulaGrado {
        $contacto = FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()->buscarPorID($programaContactoID);

        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(404, "Contacto no encontrado");
        }

        $exito = $contacto->eliminar();
        if (!$exito) {
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema.");
        }

        return new ResponsePostulaGrado(200, "Se ha eliminado con éxito");
    }
}