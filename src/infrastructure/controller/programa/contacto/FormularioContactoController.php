<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\ListarProgramasUseCase;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;


class FormularioContactoController
{

    public function __construct(private ProgramaRepository $programaRepo)
    {
    }

    public function __invoke(): ResponsePostulaGrado
    {
       $programas = [];

       foreach ((new ListarProgramasUseCase($this->programaRepo))->ejecutar() as $programa) {
           $programas[] = [
               'id' => $programa->getId(),
               'nombre' => $programa->getNombre(),
               'unidadRegional' => $programa->getUnidadRegional()->getNombre(),
           ];
       }

        return new ResponsePostulaGrado(200, "Formulario de contacto", $programas);
    }
}