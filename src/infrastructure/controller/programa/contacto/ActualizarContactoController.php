<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\ActualizarContactoUseCase;
use Src\application\programas\contactos\DTO\ContactoDTO;
use Src\application\programas\contactos\VerContactoUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarContactoController
{
    private ContactoRepository $contactoRepo;
    private ProgramaRepository $programaRepo;

    public function __construct(ContactoRepository $contactoRepo, ProgramaRepository $programaRepo)
    {
        $this->contactoRepo = $contactoRepo;
        $this->programaRepo = $programaRepo;
    }

    public function __invoke(int $contactoID, array $data): ResponsePostulaGrado
    {
        $contacto = (new VerContactoUseCase($this->contactoRepo, $this->programaRepo))->ejecutar($contactoID);
        if (sizeof($contacto) == 0) {
            return new ResponsePostulaGrado(404, 'Contacto no encontrado');
        }

        (new ActualizarContactoUseCase($this->contactoRepo, $this->programaRepo))->ejecutar(
            $contactoID,
            new ContactoDTO(
                $data['nombre'],
                $data['telefono'],
                $data['email'],                
                (int)$data['programa_id'],
                $data['observacion'],
            )
        );


        return new ResponsePostulaGrado(200, 'Contacto actualizado exitosamente');
    }
}