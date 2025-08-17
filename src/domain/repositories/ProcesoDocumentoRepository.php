<?php

namespace Src\domain\repositories;

use Src\domain\proceso\documento\ProcesoDocumento;

interface ProcesoDocumentoRepository
{
    public function crear(ProcesoDocumento $documento): bool;

    public function buscarPorID(int $id): ProcesoDocumento;

    public function listarDocumentosPorProceso(int $procesoID): array;

    public function eliminar(int $documentoID): bool;
}
