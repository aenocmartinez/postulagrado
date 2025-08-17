<?php

namespace Src\application\usecase\general;

use Src\domain\repositories\NivelEducativoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarNivelEducativoUseCase
{
    private NivelEducativoRepository $nivelRepo;

    public function __construct(NivelEducativoRepository $nivelRepo)
    {
        $this->nivelRepo = $nivelRepo;
    }

    public function ejecutar(): ResponsePostulaGrado
    {
        $niveles = $this->nivelRepo->Listar();

        $nivelesFiltrados = array_filter($niveles, function ($nivel) {
            return in_array($nivel->getNombre(), ['PREGRADO', 'POSTGRADO']);
        });
        
        
        $nivelesFiltrados = array_values($nivelesFiltrados);

        
        return new ResponsePostulaGrado(200, "Listado de niveles", $nivelesFiltrados);
    }    
}