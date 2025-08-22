<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\EliminarContactoUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EliminarContactoController
{
    public function __construct(private ContactoRepository $contactoRepo)
    {}

    public function __invoke(int $contactoID): ResponsePostulaGrado
    {
       return (new EliminarContactoUseCase($this->contactoRepo))->ejecutar($contactoID);
    }
}