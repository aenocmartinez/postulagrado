<?php

namespace Src\domain;

use Src\repositories\UnidadRegionalRepository;
use Src\shared\formato\FormatoString;

class UnidadRegional
{
    public function __construct(
        private int $id = 0, 
        private string $nombre = ""
    ){}

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
    
    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {        
        return FormatoString::capital($this->nombre);
    }    
}