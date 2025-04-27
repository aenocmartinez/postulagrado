<?php

namespace Src\admisiones\dto\proceso;

use Src\admisiones\dto\general\NivelEducativoDTO;

class ProcesoDTO
{
    private int $id;
    private string $nombre;
    private NivelEducativoDTO $nivelEducativo;
    private string $estado;


    public function __construct(string $nombre)
    {
        $this->id = 0;
        $this->nombre = $nombre;
        $this->nivelEducativo = new NivelEducativoDTO();
        $this->estado = 'ABIERTO';
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

    public function getNivelEducativo(): NivelEducativoDTO
    {
        return $this->nivelEducativo;
    }
    
    public function setNivelEducativo(int $nivelEducativoID): void
    {
        $nivelEducativo = new NivelEducativoDTO();
        $nivelEducativo->setId($nivelEducativoID);
        $this->nivelEducativo = $nivelEducativo;
    }
    
    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }
    
}