<?php

namespace Src\infrastructure\controller\programa\contacto;

use Src\application\programas\contactos\VerContactoUseCase;
use Src\application\programas\ListarProgramasUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class FormularioEditarContactoController
{
    public function __construct(
        private ContactoRepository $contactoRepo,
        private ProgramaRepository $programaRepo) {}

    public function __invoke(int $id): ResponsePostulaGrado
    {

        $contacto = (new VerContactoUseCase($this->contactoRepo))->ejecutar($id);

        if (sizeof($contacto) == 0) {
            return new ResponsePostulaGrado(404, 'Contacto no encontrado');
        }

       $programas = [];
       foreach ((new ListarProgramasUseCase($this->programaRepo))->ejecutar() as $programa) {
           $programas[] = [
               'id' => $programa->getId(),
               'nombre' => $programa->getNombre(),
               'unidadRegional' => $programa->getUnidadRegional()->getNombre(),
           ];
       }

        return new ResponsePostulaGrado(200, 'Formulario de edición cargado', [
            'contacto' => $contacto,
            'programas' => $programas
        ]);
    }
}