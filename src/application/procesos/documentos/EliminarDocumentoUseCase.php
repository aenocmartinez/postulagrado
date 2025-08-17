<?php

namespace Src\application\procesos\documentos;

use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\shared\response\ResponsePostulaGrado;
use Illuminate\Support\Facades\Storage;

class EliminarDocumentoUseCase
{
    public function __construct(private ProcesoDocumentoRepository $documentoRepo) {}

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

        $ruta = $documento->ruta()->value();
        $internalPath = str_replace('storage/', '', $ruta);
        Storage::disk('public')->delete($internalPath);        

        return new ResponsePostulaGrado(200, 'Documento eliminado exitosamente.');
    }
}
