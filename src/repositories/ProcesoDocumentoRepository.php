<?php

namespace Src\repositories;

use Src\domain\ProcesoDocumento;

interface ProcesoDocumentoRepository
{
    public function crear(ProcesoDocumento $documento): bool;

    public function buscarPorID(int $id): ProcesoDocumento;

    public function listarDocumentosPorProceso(int $procesoID): array;

    public function eliminar(int $documentoID): bool;
}
