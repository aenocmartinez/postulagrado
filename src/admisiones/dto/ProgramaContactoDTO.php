<?php

namespace Src\admisiones\dto;

class ProgramaContactoDTO {

    public function __construct(
        private string $nombre,
        private string $telefono,
        private string $email,
        private int $programaID,
        private int $id = 0,
        private string $observacion = ""
    ){}

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setTelefono(string $telefono): void {
        $this->telefono = $telefono;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setProgramaID(int $programaID): void {
        $this->programaID = $programaID;
    }

    public function setObservacion(string $observacion): void {
        $this->observacion = $observacion;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getTelefono(): string {
        return $this->telefono;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getObservacion(): string {
        return $this->observacion;
    }

    public function getProgramaID(): int {
        return $this->programaID;
    }
}