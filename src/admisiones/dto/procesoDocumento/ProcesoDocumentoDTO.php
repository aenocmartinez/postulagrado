<?php

namespace Src\admisiones\dto\procesoDocumento;

use Src\admisiones\dto\proceso\ProcesoDTO;

class ProcesoDocumentoDTO
{
    private int $id;
    private ProcesoDTO $proceso;
    private string $nombre;
    private string $ruta;

    public function __construct()
    {
        $this->id = 0;
        $this->proceso = new ProcesoDTO("");
        $this->nombre = '';
        $this->ruta = '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProceso(): ProcesoDTO
    {
        return $this->proceso;
    }

    public function setProceso(ProcesoDTO $proceso): void
    {
        $this->proceso = $proceso;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getRuta(): string
    {
        return $this->ruta;
    }

    public function setRuta(string $ruta): void
    {
        $this->ruta = $ruta;
    }
}
