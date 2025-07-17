<?php

namespace Src\admisiones\domain;

class Estudiante 
{
    private ?int $id;
    private ?string $pensum;
    private ?string $codigo;
    private ?string $documento;
    private ?string $nombre;
    private ?string $ubicacionSemestre;
    private ?string $categoria;
    private ?string $situacion;
    private ?int $totalCreditosPensum;
    private ?int $numeroCreditosPendientes;
    private ?int $numeroCreditosAreaBasica;
    private ?int $numeroCreditosAprobadosAreaBasica;
    private ?int $numeroCreditosPendientesAreaBasica;
    private ?int $numeroCreditosAreaProfundizacion;
    private ?int $numeroCreditosAprobadosAreaProfundizacion;
    private ?int $numeroCreditosPendientesAreaProfundizacion;
    private ?int $numeroCreditosElectivos;
    private ?int $numeroCreditosAprobadosElectivos;
    private ?int $numeroCreditosPendientesElectivos;
    private ?int $anio;
    private ?int $periodo;


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
        $this->anio = 0;
        $this->periodo = 0;
    }   


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        if (is_null($id)) {
            $id = 0;
        }                
        $this->id = $id;
    }

    public function getPensum(): string
    {
        return $this->pensum;
    }

    public function setPensum(?string $pensum): void
    {
        if (is_null($pensum)) {
            $pensum = "";
        }        

        $this->pensum = $pensum;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): void
    {
        if (is_null($codigo)) {
            $codigo = "";
        }        

        $this->codigo = $codigo;
    }

    public function getDocumento(): string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): void
    {
        if (is_null($documento)) {
            $documento = "";
        }        

        $this->documento = $documento;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        if (is_null($nombre)) {
            $nombre = "";
        }        

        $this->nombre = $nombre;
    }

    public function getUbicacionSemestre(): string
    {
        return $this->ubicacionSemestre;
    }

    public function setUbicacionSemestre(?string $ubicacionSemestre): void
    {
        if (is_null($ubicacionSemestre)) {
            $ubicacionSemestre = "";
        }        

        $this->ubicacionSemestre = $ubicacionSemestre;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(?string $categoria): void
    {
        if (is_null($categoria)) {
            $categoria = "";
        }        

        $this->categoria = $categoria;
    }

    public function getSituacion(): string
    {
        return $this->situacion;
    }

    public function setSituacion(?string $situacion): void
    {
        if (is_null($situacion)) {
            $situacion = "";
        }        

        $this->situacion = $situacion;
    }

    public function getTotalCreditosPensum(): int
    {
        return $this->totalCreditosPensum;
    }

    public function setTotalCreditosPensum(?int $totalCreditosPensum): void
    {
        if (is_null($totalCreditosPensum)) {
            $totalCreditosPensum = 0;
        }        

        $this->totalCreditosPensum = $totalCreditosPensum;
    }

    public function getNumeroCreditosPendientes(): int
    {
        return $this->numeroCreditosPendientes;
    } 

    public function setNumeroCreditosPendientes(?int $numeroCreditosPendientes): void
    {
        if (is_null($numeroCreditosPendientes)) {
            $numeroCreditosPendientes = 0;
        }        

        $this->numeroCreditosPendientes = $numeroCreditosPendientes;
    }

    public function getNumeroCreditosAreaBasica(): int
    {
        return $this->numeroCreditosAreaBasica;
    }

    public function setNumeroCreditosAreaBasica(?int $numeroCreditosAreaBasica): void
    {
        if (is_null($numeroCreditosAreaBasica)) {
            $numeroCreditosAreaBasica = 0;
        }        

        $this->numeroCreditosAreaBasica = $numeroCreditosAreaBasica;
    }

    public function getNumeroCreditosAprobadosAreaBasica(): int
    {
        return $this->numeroCreditosAprobadosAreaBasica;
    }

    public function setNumeroCreditosAprobadosAreaBasica(?int $numeroCreditosAprobadosAreaBasica): void
    {
        if (is_null($numeroCreditosAprobadosAreaBasica)) {
            $numeroCreditosAprobadosAreaBasica = 0;
        }        

        $this->numeroCreditosAprobadosAreaBasica = $numeroCreditosAprobadosAreaBasica;
    }

    public function getNumeroCreditosPendientesAreaBasica(): int
    {
        return $this->numeroCreditosPendientesAreaBasica;
    }

    public function setNumeroCreditosPendientesAreaBasica(?int $numeroCreditosPendientesAreaBasica): void
    {
        if (is_null($numeroCreditosPendientesAreaBasica)) {
            $numeroCreditosPendientesAreaBasica = 0;
        }        

        $this->numeroCreditosPendientesAreaBasica = $numeroCreditosPendientesAreaBasica;
    }

    public function getNumeroCreditosAreaProfundizacion(): int
    {
        return $this->numeroCreditosAreaProfundizacion;
    }

    public function setNumeroCreditosAreaProfundizacion(?int $numeroCreditosAreaProfundizacion): void
    {
        if (is_null($numeroCreditosAreaProfundizacion)) {
            $numeroCreditosAreaProfundizacion = 0;
        }        

        $this->numeroCreditosAreaProfundizacion = $numeroCreditosAreaProfundizacion;
    }

    public function getNumeroCreditosAprobadosAreaProfundizacion(): int
    {
        return $this->numeroCreditosAprobadosAreaProfundizacion;
    }

    public function setNumeroCreditosAprobadosAreaProfundizacion(?int $numeroCreditosAprobadosAreaProfundizacion): void
    {
        if (is_null($numeroCreditosAprobadosAreaProfundizacion)) {
            $numeroCreditosAprobadosAreaProfundizacion = 0;
        }        

        $this->numeroCreditosAprobadosAreaProfundizacion = $numeroCreditosAprobadosAreaProfundizacion;
    }

    public function getNumeroCreditosPendientesAreaProfundizacion(): int
    {
        return $this->numeroCreditosPendientesAreaProfundizacion;
    }

    public function setNumeroCreditosPendientesAreaProfundizacion(?int $numeroCreditosPendientesAreaProfundizacion): void
    {
        if (is_null($numeroCreditosPendientesAreaProfundizacion)) {
            $numeroCreditosPendientesAreaProfundizacion = 0;
        }        

        $this->numeroCreditosPendientesAreaProfundizacion = $numeroCreditosPendientesAreaProfundizacion;
    }

    public function getNumeroCreditosElectivos(): int
    {
        return $this->numeroCreditosElectivos;
    }

    public function setNumeroCreditosElectivos(?int $numeroCreditosElectivos): void
    {
        if (is_null($numeroCreditosElectivos)) {
            $numeroCreditosElectivos = 0;
        }        

        $this->numeroCreditosElectivos = $numeroCreditosElectivos;
    }

    public function getNumeroCreditosAprobadosElectivos(): int
    {
        return $this->numeroCreditosAprobadosElectivos;
    }

    public function setNumeroCreditosAprobadosElectivos(?int $numeroCreditosAprobadosElectivos): void
    {
        if (is_null($numeroCreditosAprobadosElectivos)) {
            $numeroCreditosAprobadosElectivos = 0;
        }

        $this->numeroCreditosAprobadosElectivos = $numeroCreditosAprobadosElectivos;
    }

    public function getNumeroCreditosPendientesElectivos(): int
    {
        return $this->numeroCreditosPendientesElectivos;
    }

    public function setNumeroCreditosPendientesElectivos(?int $numeroCreditosPendientesElectivos): void
    {
        if (is_null($numeroCreditosPendientesElectivos)) {
            $numeroCreditosPendientesElectivos = 0;
        }

        $this->numeroCreditosPendientesElectivos = $numeroCreditosPendientesElectivos;
    }

    public function setAnio(?int $anio=0) {
        if (!is_null($anio)) {
            $this->anio = $anio;
        }

        $this->anio = $anio;
    }

    public function getAnio(): ?int{
        return $this->anio;
    }    

    public function setPeriodo(?int $periodo=0) {
        if (!is_null($periodo)) {
            $this->periodo = $periodo;
        }

        $this->periodo;
    }

    public function getPeriodo(): ?int{
        return $this->periodo;
    }        

    public function existe(): bool
    {
        return $this->id > 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'pensum' => $this->getPensum(),
            'codigo' => $this->getCodigo(),
            'documento' => $this->getDocumento(),
            'nombre' => $this->getNombre(),
            'ubicacionSemestre' => $this->getUbicacionSemestre(),
            'categoria' => $this->getCategoria(),
            'situacion' => $this->getSituacion(),
            'totalCreditosPensum' => $this->getTotalCreditosPensum(),
            'numeroCreditosPendientes' => $this->getNumeroCreditosPendientes(),
            'numeroCreditosAreaBasica' => $this->getNumeroCreditosAreaBasica(),
            'numeroCreditosAprobadosAreaBasica' => $this->getNumeroCreditosAprobadosAreaBasica(),
            'numeroCreditosPendientesAreaBasica' => $this->getNumeroCreditosPendientesAreaBasica(),
            'numeroCreditosAreaProfundizacion' => $this->getNumeroCreditosAreaProfundizacion(),
            'numeroCreditosAprobadosAreaProfundizacion' => $this->getNumeroCreditosAprobadosAreaProfundizacion(),
            'numeroCreditosPendientesAreaProfundizacion' => $this->getNumeroCreditosPendientesAreaProfundizacion(),
            'numeroCreditosElectivos' => $this->getNumeroCreditosElectivos(),
            'numeroCreditosAprobadosElectivos' => $this->getNumeroCreditosAprobadosElectivos(),
            'numeroCreditosPendientesElectivos' => $this->getNumeroCreditosPendientesElectivos(),
        ];
    }

}