<?php

namespace Src\infrastructure\controller\proceso\documento;

use Src\application\procesos\documentos\ListarDocumentosDeProcesoUseCase;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarDocumentosController
{
    public function __construct(
        private ProcesoDocumentoRepository $procesoDocumentoRepo,
        private ProcesoRepository $procesoRepo,
    ){}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        return (new ListarDocumentosDeProcesoUseCase(
            $this->procesoRepo,
            $this->procesoDocumentoRepo,            
        ))->ejecutar($procesoID);
    }
}