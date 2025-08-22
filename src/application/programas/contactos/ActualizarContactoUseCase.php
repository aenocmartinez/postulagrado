<?php

namespace Src\application\programas\contactos;

use Src\application\programas\contactos\DTO\ContactoDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarContactoUseCase {

    public function __construct(
        private ContactoRepository $contactoRepo,
        private ProgramaRepository $programaRepo
    ){}

    public function ejecutar(int $programaContactoID, ContactoDTO $contactoDTO): ResponsePostulaGrado {

        /** @var \Src\domain\programa\contacto\Contacto $contacto */
        $contacto = $this->contactoRepo->buscarPorID($programaContactoID);
        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(404, 'Contacto no encontrado');
        }

        /** @var \Src\domain\programa\Programa $programa */
        $programa = $this->programaRepo->buscarPorID($contactoDTO->getProgramaID());
        if (!$programa->existe()) {
            return new ResponsePostulaGrado(404, 'Programa no encontrado');
        }


        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setTelefono($contactoDTO->getTelefono());
        $contacto->setObservacion($contactoDTO->getObservacion());
        $contacto->setEmail($contactoDTO->getEmail());
        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setProgramaID($programa->getID());

        $exito = $this->contactoRepo->actualizar($contacto);
        if (!$exito) {
            return new ResponsePostulaGrado(500, 'Error al actualizar contacto');
        }

        return new ResponsePostulaGrado(200, "Contacto actualizado exitosamente");
    }
}