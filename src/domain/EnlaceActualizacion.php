<?php

namespace Src\domain;

class EnlaceActualizacion
{
    private int $id;
    private int $procesoID;
    private int $programaID;
    private string $codigoEstudiante;
    private string $token;
    private string $usado;
    private string $fechaExpira;
    private string $fechaUso;

    public function getId(): int
    {
        return $this->id;
    }

    public function getProcesoID(): int
    {
        return $this->procesoID;
    }

    public function getProgramaID(): int {
        return $this->programaID;
    }

    public function getCodigoEstudiante(): string
    {
        return $this->codigoEstudiante;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUsado(): string
    {
        return $this->usado;
    }

    public function getFechaExpira(): string
    {
        return $this->fechaExpira;
    }

    public function getFechaUso(): string
    {
        return $this->fechaUso;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setProcesoID(int $procesoID): self
    {
        $this->procesoID = $procesoID;
        return $this;
    }

    public function setProgramaID(int $programaID): self
    {
        $this->programaID = $programaID;
        return $this;
    }    

    public function setCodigoEstudiante(string $codigoEstudiante): self
    {
        $this->codigoEstudiante = $codigoEstudiante;
        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function setUsado(string $usado): self
    {
        $this->usado = $usado;
        return $this;
    }

    public function setFechaExpira(string $fechaExpira): self
    {
        $this->fechaExpira = $fechaExpira;
        return $this;
    }

    public function setFechaUso(string $fechaUso): self
    {
        $this->fechaUso = $fechaUso;
        return $this;
    }
}
