<?php

namespace Src\usecase\procesoDocumentos;

use Src\repositories\ProcesoDocumentoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ConsultarDocumentoDeProcesoUseCase
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

        return new ResponsePostulaGrado(
            200,
            'Documento encontrado correctamente.',
            $documento
        );
    }
}
