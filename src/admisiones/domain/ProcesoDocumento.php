<?php

namespace Src\admisiones\domain;

use Src\admisiones\repositories\ProcesoDocumentoRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProcesoDocumento
{
    private int $id;
    private Proceso $proceso;
    private string $nombre;
    private string $ruta;
    private ProcesoDocumentoRepository $repository;

    public function __construct(ProcesoDocumentoRepository $repository)
    {
        $this->id = 0;
        $this->proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getActividadRepository(),
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
        );

        $this->nombre = '';
        $this->ruta = '';
        $this->repository = $repository;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getProceso(): Proceso
    {
        return $this->proceso;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getRuta(): string
    {
        return $this->ruta;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setProceso(Proceso $proceso): void
    {
        $this->proceso = $proceso;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setRuta(string $ruta): void
    {
        $this->ruta = $ruta;
    }

    // Acciones principales
    public function crear(): bool
    {
        return $this->repository->crear($this);
    }

    public function eliminar(): bool
    {
        return $this->repository->eliminar($this->id);
    }

    public function existe(): bool
    {
        return $this->id > 0;
    }
}
