<?php

namespace Src\admisiones\usecase\procesoDocumentos;

use Src\admisiones\repositories\ProcesoDocumentoRepository;
use Src\shared\response\ResponsePostulaGrado;

class EliminarDocumentoDeProcesoUseCase
{
    private ProcesoDocumentoRepository $documentoRepo;

    public function __construct(ProcesoDocumentoRepository $documentoRepo)
    {
        $this->documentoRepo = $documentoRepo;
    }

    public function ejecutar(int $documentoID): ResponsePostulaGrado
    {
        $documento = $this->documentoRepo->buscarPorID($documentoID);

        if (!$documento->existe()) {
            return new ResponsePostulaGrado(404, 'Documento no encontrado.');
        }

        $resultado = $this->documentoRepo->eliminar($documentoID);

        if (!$resultado) {
            return new ResponsePostulaGrado(500, 'Error al eliminar el documento.');
        }

        return new ResponsePostulaGrado(200, 'Documento eliminado exitosamente.');
    }
}
