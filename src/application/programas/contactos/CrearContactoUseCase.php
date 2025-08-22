<?php

namespace Src\application\programas\contactos;

use Src\application\programas\contactos\DTO\ContactoDTO;
use Src\domain\programa\contacto\Contacto;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearContactoUseCase
{
    public function __construct(
        private ContactoRepository $contactoRepo,
        private ProgramaRepository $programaRepo
    ) {}

    public function ejecutar(ContactoDTO $contactoDTO): ResponsePostulaGrado {

        $programa = $this->programaRepo->buscarPorID($contactoDTO->getProgramaID());
        if (!$programa->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        $contacto = new Contacto();
        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setTelefono($contactoDTO->getTelefono());
        $contacto->setEmail($contactoDTO->getEmail());
        $contacto->setObservacion($contactoDTO->getObservacion());
        $contacto->setProgramaID($programa->getId());

        $exito = $this->contactoRepo->crear($contacto); 
        if (!$exito) {
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema");
        }
        
        return new ResponsePostulaGrado(201, "Contacto creado exitosamente.", $contacto);
    }
}