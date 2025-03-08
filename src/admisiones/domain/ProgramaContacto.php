<?php

namespace Src\admisiones\domain;

class ProgramaContacto 
{
    private string $nombre;
    private string $telefono;
    private string $email;
    private string $observacion;
    private Programa $programa;

    public function __construct(private int $id = 0) {}

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

    public function setObservacion(string $observacion): void
    {
        $this->observacion = $observacion;
    }

    public function getPrograma(): Programa
    {
        return $this->programa;
    }

    public function setPrograma(Programa $programa): void
    {
        $this->programa = $programa;
    }
}
