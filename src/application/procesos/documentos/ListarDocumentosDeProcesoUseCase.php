<?php

namespace Src\application\procesos\documentos;

use Src\application\procesos\documentos\DTO\ProcesoDocumentoDTO;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarDocumentosDeProcesoUseCase
{    
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ProcesoDocumentoRepository $documentoRepo,
    ) {}

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'Proceso no encontrado.');
        }        

        $documentos = $this->documentoRepo->listarDocumentosPorProceso($proceso->getId());

        $itemDocumentoDTO = new ProcesoDocumentoDTO();
        $itemDocumentoDTO->nombreProceso = $proceso->getNombre();
        $itemDocumentoDTO->procesoID = $proceso->getId();
        $itemDocumentoDTO->documentos = array_map(
            fn($doc) => $doc->toPrimitives(),
            $documentos
        );

        return new ResponsePostulaGrado(
            200,
            'Listado de documentos del proceso obtenido correctamente.',
            $itemDocumentoDTO,
        );
    }
}
