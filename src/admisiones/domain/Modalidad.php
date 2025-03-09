<?php

namespace Src\admisiones\domain;

use Src\admisiones\repositories\ModalidadRepository;

class Modalidad
{
    public function __construct(
        private ModalidadRepository $repository,
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
        return $this->nombre;
    }    
}