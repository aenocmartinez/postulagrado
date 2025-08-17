<?php

namespace Src\application\procesos\DTO;

class CrearProcesoDTO
{
    private string $nombre;
    private int $nivelEducativoId;

    public function __construct(string $nombre, int $nivelEducativoId)
    {
        $this->nombre = $nombre;
        $this->nivelEducativoId = $nivelEducativoId;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getNivelEducativoId(): int
    {
        return $this->nivelEducativoId;
    }
}
