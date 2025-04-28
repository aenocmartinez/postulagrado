<?php

namespace Src\admisiones\usecase\procesoDocumentos;

use Src\admisiones\domain\ProcesoDocumento;
use Src\admisiones\dto\procesoDocumento\ProcesoDocumentoDTO;
use Src\admisiones\repositories\ProcesoDocumentoRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class AdjuntarDocumentoAProcesoUseCase
{
    private ProcesoDocumentoRepository $procesoDocumentoRepo;
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoDocumentoRepository $procesoDocumentoRepo, ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->procesoDocumentoRepo = $procesoDocumentoRepo;
    }

    public function ejecutar(ProcesoDocumentoDTO $procesoDocumentoDTO): ResponsePostulaGrado
    {

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoDocumentoDTO->getProceso()->getId());
        if (is_null($proceso)) {
            return new ResponsePostulaGrado(404, 'Proceso no encontrado.');
        }

        $documento = new ProcesoDocumento($this->procesoDocumentoRepo);

        $documento->setProceso($proceso);
        $documento->setNombre($procesoDocumentoDTO->getNombre());
        $documento->setRuta($procesoDocumentoDTO->getRuta());

        $resultado = $documento->crear();

        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'No fue posible adjuntar el documento al proceso.');
        }

        return new ResponsePostulaGrado(201, 'Documento adjuntado exitosamente al proceso.');
    }
}
