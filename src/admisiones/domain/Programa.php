<?php

namespace Src\admisiones\domain;

class Programa 
{
    private string $nombre;
    private int $snies;
    private int $codigo;
    private Metodologia $metodologia;
    private NivelEducativo $nivelEducativo;
    private Modalidad $modalidad;
    private Jornada $jornada;
    private UnidadRegional $unidadRegional;

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

    public function getSnies(): int
    {
        return $this->snies;
    }

    public function setSnies(int $snies): void
    {
        $this->snies = $snies;
    }

    public function getCodigo(): int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getMetodologia(): Metodologia
    {
        return $this->metodologia;
    }

    public function setMetodologia(Metodologia $metodologia): void
    {
        $this->metodologia = $metodologia;
    }

    public function getNivelEducativo(): NivelEducativo
    {
        return $this->nivelEducativo;
    }

    public function setNivelEducativo(NivelEducativo $nivelEducativo): void
    {
        $this->nivelEducativo = $nivelEducativo;
    }

    public function getModalidad(): Modalidad
    {
        return $this->modalidad;
    }

    public function setModalidad(Modalidad $modalidad): void
    {
        $this->modalidad = $modalidad;
    }

    public function getJornada(): Jornada
    {
        return $this->jornada;
    }

    public function setJornada(Jornada $jornada): void
    {
        $this->jornada = $jornada;
    }

    public function getUnidadRegional(): UnidadRegional
    {
        return $this->unidadRegional;
    }

    public function setUnidadRegional(UnidadRegional $unidadRegional): void
    {
        $this->unidadRegional = $unidadRegional;
    }
}
