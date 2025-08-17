<?php

namespace Src\infrastructure\controller\proceso\documento;

use Src\application\procesos\documentos\DTO\GuardarDocumentoDTO;
use Src\application\procesos\documentos\GuardarDocumentoUseCase;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;

class GuardarDocumentoController
{
    public function __construct(
        private ProcesoDocumentoRepository $procesoDocumentoRepo,
        private ProcesoRepository $procesoRepo,
    ){}

    public function __invoke(GuardarDocumentoDTO $guardarDocumentoDTO)
    {
        return (new GuardarDocumentoUseCase($this->procesoDocumentoRepo, $this->procesoRepo))->ejecutar($guardarDocumentoDTO);        
    }
    
}