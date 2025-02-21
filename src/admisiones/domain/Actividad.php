<?php

namespace Src\admisiones\domain;

class Actividad {
    private int $id;
    private string $descripcion;
    private $fechaInicio;
    private $fechaFin;

    public function __construct()
    {
        $this->id = 0;
        $this->descripcion = "";
        $this->fechaInicio = "";
        $this->fechaFin = "";
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function getDescripcion(): string {
        return $this->descripcion;
    }

    public function setFechaInicio($fechaInicio): void {
        $this->fechaInicio = $fechaInicio;
    }

    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    public function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }

    public function getFechaFin() {
        return $this->fechaFin;
    }

    public function existe(): bool {
        return $this->id > 0;
    }
}