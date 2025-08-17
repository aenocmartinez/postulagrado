<?php

namespace Src\application\procesos\documentos;

use Src\application\procesos\documentos\DTO\GuardarDocumentoDTO;
use Src\domain\proceso\documento\ProcesoDocumento as DocumentoProcesoDocumento;
use Src\domain\proceso\documento\valueObject\NombreDocumento;
use Src\domain\proceso\documento\valueObject\ProcesoDocumentoId;
use Src\domain\proceso\documento\valueObject\ProcesoId;
use Src\domain\proceso\documento\valueObject\RutaDocumento;
use Src\domain\ProcesoDocumento;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\dto\procesoDocumento\ProcesoDocumentoDTO;
use Src\shared\response\ResponsePostulaGrado;

class GuardarDocumentoUseCase
{    
    public function __construct
    (
        private ProcesoDocumentoRepository $procesoDocumentoRepo, 
        private ProcesoRepository $procesoRepo
    ){}

    public function ejecutar(GuardarDocumentoDTO $guardarDocumentoDTO): ResponsePostulaGrado
    {

        $proceso = $this->procesoRepo->buscarProcesoPorId($guardarDocumentoDTO->procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'Proceso no encontrado.');
        }

        $documento = new DocumentoProcesoDocumento(
            null, 
            new ProcesoId($guardarDocumentoDTO->procesoID),
            new NombreDocumento($guardarDocumentoDTO->nombre),
            new RutaDocumento($guardarDocumentoDTO->ruta),
        );

        $resultado = $this->procesoDocumentoRepo->crear($documento);

        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'No fue posible guardar el documento.');
        }

        return new ResponsePostulaGrado(201, 'Documento guardado exitosamente.');
    }
}
