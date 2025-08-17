<?php

namespace Src\usecase\programaContacto;

use Src\domain\ProgramaContacto;
use Src\dto\ProgramaContactoDTO;
use Src\repositories\ProgramaContactoRepository;
use Src\shared\di\FabricaDeRepositorios;
use Src\shared\response\ResponsePostulaGrado;

class CrearContactoUseCase
{
    public function __construct(
        private ProgramaContactoRepository $programaContactoRepo
    ) {}

    public function ejecutar(ProgramaContactoDTO $contactoDTO): ResponsePostulaGrado {

        $programa = FabricaDeRepositorios::getInstance()->getProgramaRepository()->buscarPorID($contactoDTO->getProgramaID());
        if (!$programa->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        $contacto = new ProgramaContacto(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );

        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setTelefono($contactoDTO->getTelefono());
        $contacto->setEmail($contactoDTO->getEmail());
        $contacto->setObservacion($contactoDTO->getObservacion());
        $contacto->setPrograma($programa);

        $exito = $contacto->crear();
        if (!$exito) {
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema");
        }
        
        return new ResponsePostulaGrado(201, "Contacto creado exitosamente.", $contacto);
    }
}