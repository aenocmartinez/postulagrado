<?php

namespace Src\application\programas\contactos;

use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarContactosUseCase {

    public function __construct(
        private ContactoRepository $contactoRepo
    ) {}

    public function ejecutar(): array {
        $contactos = [];
        
        foreach($this->contactoRepo->listar() as $contacto) {
            $contactos[] = [
                'id' => $contacto->getId(),
                'nombre' => $contacto->getNombre(),
                'telefono' => $contacto->getTelefono(),
                'email' => $contacto->getEmail(),
                'observacion' => $contacto->getObservacion(),
                'programaID' => $contacto->getProgramaID(),
                'programaNombre' => $contacto->getProgramaNombre()
            ];
        }

        return $contactos;
    }
}