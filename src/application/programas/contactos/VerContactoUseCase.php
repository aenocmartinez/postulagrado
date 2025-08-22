<?php

namespace Src\application\programas\contactos;

use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class VerContactoUseCase {

    public function __construct(
        private ContactoRepository $programaContactoRepo,
    ) {}    

    public function ejecutar(int $contactoID): array {

        $contacto = $this->programaContactoRepo->buscarPorID($contactoID);
        if (!$contacto->existe()) {
            return [];
        }


        return [
            'id' => $contacto->getId(),
            'nombre' => $contacto->getNombre(),
            'telefono' => $contacto->getTelefono(),
            'email' => $contacto->getEmail(),
            'programaID' => $contacto->getProgramaId(),
            'programaNombre' => $contacto->getProgramaNombre(),
            'observacion' => $contacto->getObservacion()
        ];
    }    
}