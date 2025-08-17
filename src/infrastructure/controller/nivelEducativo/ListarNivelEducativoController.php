<?php

namespace Src\infrastructure\controller\nivelEducativo;

use Src\application\nivelEducativo\ListarNivelEducativoUseCase;
use Src\domain\repositories\NivelEducativoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarNivelEducativoController
{
    public function __construct(private NivelEducativoRepository $nivelEducativoRepo)
    {
    }

    public function __invoke(): ResponsePostulaGrado    
    {        
        $nivelesEducativos = (new ListarNivelEducativoUseCase($this->nivelEducativoRepo))->ejecutar();
        return new ResponsePostulaGrado(200, "Niveles educativos obtenidos exitosamente.", $nivelesEducativos);
    }   
}