<?php

namespace Src\admisiones\dto\general;

class NivelEducativoDTO
{
    private int $id;
    private string $nombre;

    public function __construct(string $nombre = '')
    {
        $this->id = 0;
        $this->nombre = $nombre;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}