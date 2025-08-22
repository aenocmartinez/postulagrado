<?php

namespace Src\application\programas\contactos;

use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EliminarContactoUseCase {

    public function __construct(
        private ContactoRepository $contactoRepo
    ){}

    public function ejecutar(int $programaContactoID): ResponsePostulaGrado {
        $contacto = $this->contactoRepo->buscarPorID($programaContactoID);
        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(404, "Contacto no encontrado");
        }

        $exito = $this->contactoRepo->eliminar($contacto->getId());
        if (!$exito) {
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema.");
        }

        return new ResponsePostulaGrado(200, "Contacto eliminado con éxito");
    }
}