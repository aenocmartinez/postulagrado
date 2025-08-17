<?php

namespace Src\application\procesos\documentos\DTO;

class ProcesoDocumentoDTO
{
    public string $nombreProceso;
    public int $procesoID;
    public array $documentos = [];
}