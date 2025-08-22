<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\VerContactoUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class VerContactoController {

    public function __construct(private ContactoRepository $contactoRepo) {}

    public function __invoke(int $id): ResponsePostulaGrado
    {
        $contacto = (new VerContactoUseCase($this->contactoRepo))->ejecutar($id);

        if (sizeof($contacto) == 0) {
            return new ResponsePostulaGrado(404, 'Contacto no encontrado');
        }

        return new ResponsePostulaGrado(200, 'Contacto encontrado', $contacto);
    }
}