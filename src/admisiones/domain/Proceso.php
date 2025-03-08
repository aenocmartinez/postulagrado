<?php

namespace Src\admisiones\domain;

use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\dao\mysql\ProcesoDao;
use Src\admisiones\repositories\ProcesoRepository;

class Proceso 
{    
    private string              $nombre;
    private string              $nivelEducativo;
    private Calendario          $calendario;
    private ProcesoRepository   $repository;

    /** @var Documento[] $documentos */
    private $documentos = [];
    
    public function __construct(
        ProcesoRepository   $repository,
        private int $id = 0, 
        private string $estado = "Abierto"
    ) {

        $this->repository = $repository;
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
        $exito = $this->repository->crearProceso($this);
        if (!$exito) {
            return false;
        }

        $calendario = new Calendario();
        $calendario->setProceso($this);

        return CalendarioDao::crearCalendario($calendario);
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

    public function getActividades(): array {
        return CalendarioDao::listarActividades($this->id);
    }

    public function agregarActividad(string $descripcion, $fechaInicio, $fechaFin): bool {
        $actividad = new Actividad();
        $actividad->setDescripcion($descripcion);
        $actividad->setFechaInicio($fechaInicio);
        $actividad->setFechaFin($fechaFin);

        return CalendarioDao::agregarActividad($this->id, $actividad);
    }

    public function actualizarActividad(Actividad $actividad): bool {
        return CalendarioDao::actualizarActividad($actividad);
    }

    public function quitarActividad(Actividad $actividad): bool {
        return CalendarioDao::eliminarActividad($actividad->getId());
    }
}