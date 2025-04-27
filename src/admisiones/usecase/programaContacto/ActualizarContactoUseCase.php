<?php

namespace Src\admisiones\usecase\programaContacto;

use Illuminate\Support\Facades\Cache;
use Src\admisiones\dto\ProgramaContactoDTO;
use Src\admisiones\repositories\ProgramaContactoRepository;
use Src\shared\di\FabricaDeRepositorios;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarContactoUseCase {

    public function __construct(
        private ProgramaContactoRepository $programContactoRepo
    ){}

    public function ejecutar(int $programaContactoID, ProgramaContactoDTO $contactoDTO): ResponsePostulaGrado {

        $contacto = FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()->buscarPorID($programaContactoID);
        if (!$contacto->existe()) {
            return new ResponsePostulaGrado(404, "Contacto no encontrado");
        }

        $programa = FabricaDeRepositorios::getInstance()->getProgramaRepository()->buscarPorID($contactoDTO->getProgramaID());
        if (!$programa->existe()) {
            return new ResponsePostulaGrado(404, "Programa no encontrado");
        }

        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setTelefono($contactoDTO->getTelefono());
        $contacto->setObservacion($contactoDTO->getObservacion());
        $contacto->setEmail($contactoDTO->getEmail());
        $contacto->setNombre($contactoDTO->getNombre());
        $contacto->setPrograma($programa);

        $exito = $contacto->actualizar();
        if (!$exito) {
            return new ResponsePostulaGrado(500, "Ha ocurrido un error en el sistema.");
        }

        Cache::forget('contacto_' . $programaContactoID);

        return new ResponsePostulaGrado(200, "Contacto acutalizado exitosamente");
    }
}