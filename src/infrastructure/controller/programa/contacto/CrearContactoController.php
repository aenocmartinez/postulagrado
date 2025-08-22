<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\CrearContactoUseCase;
use Src\application\programas\contactos\DTO\ContactoDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearContactoController
{
    public function __construct(
        private ContactoRepository $contactoRepo,
        private ProgramaRepository $programaRepo
    ){}

    public function __invoke(array $data): ResponsePostulaGrado
    {
        $crearContacto = new CrearContactoUseCase($this->contactoRepo, $this->programaRepo);
        $contactoDto = new ContactoDTO(
                            $data['nombre'],
                            $data['telefono'],
                            $data['email'],
                            $data['programa_id'],
                            $data['observacion']
                        );
        return $crearContacto->ejecutar($contactoDto);
    }
}