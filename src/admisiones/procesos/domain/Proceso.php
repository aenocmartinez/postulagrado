<?php

namespace Src\admisiones\procesos\domain;

use Src\admisiones\procesos\dao\mysql\ProcesoDao;
use Src\admisiones\procesos\domain\ProcesoRepository;

class Proceso 
{
    private int $id;
    private string $nombre;
    private string $nivelEducativo;
    private string $rutaArchivoActoAdmnistrativo;
    private string $estado;
    private Calendario $calendario;
    private ProcesoRepository $repository;

    public function __construct() {
        $this->rutaArchivoActoAdmnistrativo = '';
        $this->nivelEducativo = "";
        $this->estado = "Abierto";
        $this->nombre = "";
        $this->id = 0;

        $this->repository = new ProcesoDao();
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNivelEducativo(string $nivelEducativo): void {
        $this->nivelEducativo = $nivelEducativo;
    }

    public function getNivelEducativo(): string {
        return $this->nivelEducativo;
    }

    public function setRutaArchivoActoAdministrativo(string $rutaArchivoActoAdmnistrativo): void {
        $this->rutaArchivoActoAdmnistrativo = $rutaArchivoActoAdmnistrativo;
    }

    public function getRutaArchivoActoAdministrativo(): string {
        return $this->rutaArchivoActoAdmnistrativo;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setCalendario(Calendario $calendario): void {
        $this->calendario = $calendario;
    }

    public function getCalendario(): Calendario {
        return $this->calendario;
    }

    public function crear(): bool {
        return $this->repository->crearProceso($this);
    }

    public function eliminar(): bool {
        return $this->repository->eliminarProceso($this->id);
    }

    public function actualizar(): bool {
        return $this->repository->actualizarProceso($this);
    }    

    public function existe(): bool {
        return $this->id > 0;
    }
}