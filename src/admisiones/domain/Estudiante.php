<?php

namespace Src\admisiones\domain;

class Estudiante 
{
    private int $id;
    private string $pensum;
    private string $codigo;
    private string $documento;
    private string $nombre;
    private string $ubicacionSemestre;
    private string $categoria;
    private string $situacion;
    private int $totalCreditosPensum;
    private int $numeroCreditosPendientes;
    private int $numeroCreditosAreaBasica;
    private int $numeroCreditosAprobadosAreaBasica;
    private int $numeroCreditosPendientesAreaBasica;
    private int $numeroCreditosAreaProfundizacion;
    private int $numeroCreditosAprobadosAreaProfundizacion;
    private int $numeroCreditosPendientesAreaProfundizacion;
    private int $numeroCreditosElectivos;
    private int $numeroCreditosAprobadosElectivos;
    private int $numeroCreditosPendientesElectivos;


    public function __construct() {
        $this->id = 0;
        $this->pensum = '';
        $this->codigo = '';
        $this->documento = '';
        $this->nombre = '';
        $this->ubicacionSemestre = '';
        $this->categoria = '';
        $this->situacion = '';
        $this->totalCreditosPensum = 0;
        $this->numeroCreditosPendientes = 0;
        $this->numeroCreditosAreaBasica = 0;
        $this->numeroCreditosAprobadosAreaBasica = 0;
        $this->numeroCreditosPendientesAreaBasica = 0;
        $this->numeroCreditosAreaProfundizacion = 0;
        $this->numeroCreditosAprobadosAreaProfundizacion = 0;
        $this->numeroCreditosPendientesAreaProfundizacion = 0;
        $this->numeroCreditosElectivos = 0;
        $this->numeroCreditosAprobadosElectivos = 0;
        $this->numeroCreditosPendientesElectivos = 0;
    }   


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPensum(): string
    {
        return $this->pensum;
    }

    public function setPensum(string $pensum): void
    {
        $this->pensum = $pensum;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getDocumento(): string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): void
    {
        $this->documento = $documento;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getUbicacionSemestre(): string
    {
        return $this->ubicacionSemestre;
    }

    public function setUbicacionSemestre(string $ubicacionSemestre): void
    {
        $this->ubicacionSemestre = $ubicacionSemestre;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function getSituacion(): string
    {
        return $this->situacion;
    }

    public function setSituacion(string $situacion): void
    {
        $this->situacion = $situacion;
    }

    public function getTotalCreditosPensum(): int
    {
        return $this->totalCreditosPensum;
    }

    public function setTotalCreditosPensum(int $totalCreditosPensum): void
    {
        $this->totalCreditosPensum = $totalCreditosPensum;
    }

    public function getNumeroCreditosPendientes(): int
    {
        return $this->numeroCreditosPendientes;
    } 

    public function setNumeroCreditosPendientes(int $numeroCreditosPendientes): void
    {
        $this->numeroCreditosPendientes = $numeroCreditosPendientes;
    }

    public function getNumeroCreditosAreaBasica(): int
    {
        return $this->numeroCreditosAreaBasica;
    }

    public function setNumeroCreditosAreaBasica(int $numeroCreditosAreaBasica): void
    {
        $this->numeroCreditosAreaBasica = $numeroCreditosAreaBasica;
    }

    public function getNumeroCreditosAprobadosAreaBasica(): int
    {
        return $this->numeroCreditosAprobadosAreaBasica;
    }

    public function setNumeroCreditosAprobadosAreaBasica(int $numeroCreditosAprobadosAreaBasica): void
    {
        $this->numeroCreditosAprobadosAreaBasica = $numeroCreditosAprobadosAreaBasica;
    }

    public function getNumeroCreditosPendientesAreaBasica(): int
    {
        return $this->numeroCreditosPendientesAreaBasica;
    }

    public function setNumeroCreditosPendientesAreaBasica(int $numeroCreditosPendientesAreaBasica): void
    {
        $this->numeroCreditosPendientesAreaBasica = $numeroCreditosPendientesAreaBasica;
    }

    public function getNumeroCreditosAreaProfundizacion(): int
    {
        return $this->numeroCreditosAreaProfundizacion;
    }

    public function setNumeroCreditosAreaProfundizacion(int $numeroCreditosAreaProfundizacion): void
    {
        $this->numeroCreditosAreaProfundizacion = $numeroCreditosAreaProfundizacion;
    }

    public function getNumeroCreditosAprobadosAreaProfundizacion(): int
    {
        return $this->numeroCreditosAprobadosAreaProfundizacion;
    }

    public function setNumeroCreditosAprobadosAreaProfundizacion(int $numeroCreditosAprobadosAreaProfundizacion): void
    {
        $this->numeroCreditosAprobadosAreaProfundizacion = $numeroCreditosAprobadosAreaProfundizacion;
    }

    public function getNumeroCreditosPendientesAreaProfundizacion(): int
    {
        return $this->numeroCreditosPendientesAreaProfundizacion;
    }

    public function setNumeroCreditosPendientesAreaProfundizacion(int $numeroCreditosPendientesAreaProfundizacion): void
    {
        $this->numeroCreditosPendientesAreaProfundizacion = $numeroCreditosPendientesAreaProfundizacion;
    }

    public function getNumeroCreditosElectivos(): int
    {
        return $this->numeroCreditosElectivos;
    }

    public function setNumeroCreditosElectivos(int $numeroCreditosElectivos): void
    {
        $this->numeroCreditosElectivos = $numeroCreditosElectivos;
    }

    public function getNumeroCreditosAprobadosElectivos(): int
    {
        return $this->numeroCreditosAprobadosElectivos;
    }

    public function setNumeroCreditosAprobadosElectivos(int $numeroCreditosAprobadosElectivos): void
    {
        $this->numeroCreditosAprobadosElectivos = $numeroCreditosAprobadosElectivos;
    }

    public function getNumeroCreditosPendientesElectivos(): int
    {
        return $this->numeroCreditosPendientesElectivos;
    }

    public function setNumeroCreditosPendientesElectivos(int $numeroCreditosPendientesElectivos): void
    {
        $this->numeroCreditosPendientesElectivos = $numeroCreditosPendientesElectivos;
    }

    public function existe(): bool
    {
        return $this->id > 0;
    }
}