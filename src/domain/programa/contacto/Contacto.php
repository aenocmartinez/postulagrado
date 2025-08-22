<?php

namespace Src\domain\programa\contacto;

class Contacto 
{
    private string $nombre;
    private string $telefono;
    private string $email;
    private string $observacion;
    private int $programaID;
    private string $programaNombre;

    public function __construct(
        private int $id = 0
        ) {}

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

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getObservacion(): string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): void
    {
        if (empty($observacion)) {
            $observacion = "No hay observaciones";
        }
        
        $this->observacion = $observacion;
    }

    public function getProgramaID(): int
    {
        return $this->programaID;
    }

    public function setProgramaID(int $programaID): void
    {
        $this->programaID = $programaID;
    }

    public function setProgramaNombre(string $programaNombre): void
    {
        $this->programaNombre = $programaNombre;
    }

    public function getProgramaNombre(): string
    {
        return $this->programaNombre;
    }    

    public function existe(): bool {
        return $this->id > 0;
    }
}
