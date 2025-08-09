<?php

namespace Src\domain;

class Documento
{
    public function __construct(
                private int $id = 0, 
                private string $nombre = "", 
                private string $ruta = ""
    ){}

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setRuta(string $ruta){
        $this->ruta = $ruta;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getRuta(): string {
        return $this->ruta;
    }
}