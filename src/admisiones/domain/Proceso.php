<?php

namespace Src\admisiones\domain;

use Carbon\Carbon;
use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\formato\FormatoFecha;

class Proceso 
{    
    private string              $nombre;
    private string              $nivelEducativo;
    private Calendario          $calendario;

    /** @var Documento[] $documentos */
    private $documentos = [];
    
    public function __construct(
        private ProcesoRepository   $repository,
        private CalendarioRepository $calendarioRepo,
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
        $exito = $this->repository->quitarTodosLosPrograma($this->id);
        if (!$exito) {
            return false;
        }
        
        return $this->repository->eliminarProceso($this->id);
    }

    public function actualizar(): bool {
        return $this->repository->actualizarProceso($this);
    }    

    public function existe(): bool {
        return $this->id > 0;
    }

    public function getActividades(): array {
        return $this->calendarioRepo->listarActividades($this->id);
    }

    public function getActividadesPorEstadoTemporal(): array 
    {
        $actividadesClasificadas = [
            'EnCurso' => [],
            'Finalizadas' => [],
            'Programadas' => [],
            'ProximasIniciar' => []
        ];

        $fechaActual = Carbon::now();

        foreach ($this->getActividades() as $actividad) 
        {
            $fechaInicio = FormatoFecha::ConvertirStringAObjetoCarbon($actividad->getFechaInicio());

            $fechaFin = FormatoFecha::ConvertirStringAObjetoCarbon($actividad->getFechaFin());

            if ($fechaInicio->lessThanOrEqualTo($fechaActual) && $fechaFin->greaterThanOrEqualTo($fechaActual)) 
            {
                $actividadesClasificadas['EnCurso'][] = $actividad;
            } 
            elseif ($fechaFin->lessThan($fechaActual)) 
            {
                $actividadesClasificadas['Finalizadas'][] = $actividad;
            } 
            elseif ($fechaInicio->greaterThan($fechaActual)) 
            {
                $diasParaIniciar = $fechaInicio->greaterThan($fechaActual) ? $fechaActual->diffInDays($fechaInicio) : 0;

                if ($diasParaIniciar <= 7) 
                {
                    $actividadesClasificadas['ProximasIniciar'][] = $actividad;
                } 
                else 
                {
                    $actividadesClasificadas['Programadas'][] = $actividad;
                }
            }
        }

        return $actividadesClasificadas;
    }
    
    public function agregarActividad(string $descripcion, $fechaInicio, $fechaFin): bool {
        $actividad = new Actividad();
        $actividad->setDescripcion($descripcion);
        $actividad->setFechaInicio($fechaInicio);
        $actividad->setFechaFin($fechaFin);

        return $this->calendarioRepo->agregarActividad($this->id, $actividad);
    }

    public function actualizarActividad(Actividad $actividad): bool {
        return $this->calendarioRepo->actualizarActividad($actividad);
    }

    public function quitarActividad(Actividad $actividad): bool {
        return $this->calendarioRepo->eliminarActividad($actividad->getId());
    }

    public function agregarPrograma(Programa $programa): bool {
        return $this->repository->agregarPrograma($this->id, $programa->getId());
    }

    public function quitarPrograma(Programa $programa): bool {
        return $this->repository->quitarPrograma($this->id, $programa->getId());
    }

    public function getProgramas(): array {
        return $this->repository->listarProgramas($this->id);
    }
}