<?php

namespace Src\infrastructure\controller\proceso\documento;

use Src\application\procesos\documentos\EliminarDocumentoUseCase;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EliminarDocumentoController
{

    public function __construct(
        private ProcesoDocumentoRepository $procesoDocumentoRepo,
        private ProcesoRepository $procesoRepo,
    ){}

    public function __invoke(int $documentoID): ResponsePostulaGrado
    {
        return (new EliminarDocumentoUseCase($this->procesoDocumentoRepo))->ejecutar($documentoID);
    }
    
}