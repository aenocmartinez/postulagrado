<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\ListarContactosUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\shared\response\ResponsePostulaGrado;


class ListarContactosController 
{
    public function __construct(
        private ContactoRepository $contactoRepo
    ) {}   

    public function __invoke(): ResponsePostulaGrado {

        return new ResponsePostulaGrado(
                200, 
                "Lista de contactos obtenida correctamente", 
                (new ListarContactosUseCase($this->contactoRepo))->ejecutar()
            );
    }
}