<?php

namespace Src\usecase\procesoDocumentos;

use Src\dto\procesoDocumento\ProcesoDocumentoDTO;
use Src\dto\proceso\ProcesoDTO;
use Src\repositories\ProcesoDocumentoRepository;
use Src\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarDocumentosDeProcesoUseCase
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;  
    }

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'Proceso no encontrado.');
        }        


        return new ResponsePostulaGrado(
            200,
            'Listado de documentos del proceso obtenido correctamente.',
            $proceso,
        );
    }
}
