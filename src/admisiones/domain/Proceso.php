<?php

namespace Src\admisiones\domain;

use Carbon\Carbon;
use Src\admisiones\dto\proceso\ProcesoDTO;
use Src\admisiones\repositories\ActividadRepository;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\admisiones\repositories\ProcesoDocumentoRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\di\FabricaDeRepositorios;
use Src\shared\formato\FormatoFecha;

class Proceso 
{    
    private string              $nombre;
    private ProcesoRepository   $repository;
    private ActividadRepository $actividadRepo;
    private NivelEducativoRepository $nivelRepo;
    private NivelEducativo      $nivelEducativo;
    private ProcesoDocumentoRepository $documentoRepo;


    /** @var Actividades[] $documentos */
    private $actividades = [];
    
    public function __construct(
        private int $id = 0, 
        private string $estado = "ABIERTO"
    ) {

        $this->documentoRepo = FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository();
        $this->nivelRepo = FabricaDeRepositorios::getInstance()->getNivelEducativoRepository();
        $this->actividadRepo = FabricaDeRepositorios::getInstance()->getActividadRepository();
        $this->repository = FabricaDeRepositorios::getInstance()->getProcesoRepository();
    }

    public function setRepository(ProcesoRepository $repository): void {
        $this->repository = $repository;
    }

    public function setActividadRepo(ActividadRepository $actividadRepo): void {
        $this->actividadRepo = $actividadRepo;
    }

    public function setNivelRepo(NivelEducativoRepository $nivelRepo): void {
        $this->nivelRepo = $nivelRepo;
    }

    public function setNivelEducativoRepo(NivelEducativoRepository $nivelRepo): void {
        $this->nivelRepo = $nivelRepo;
    }

    public function setDocumentoRepo(ProcesoDocumentoRepository $documentoRepo): void {
        $this->documentoRepo = $documentoRepo;
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

    public function setNivelEducativo(NivelEducativo $nivelEducativo): void {
        $this->nivelEducativo = $nivelEducativo;
    }

    public function getNivelEducativo(): NivelEducativo {
        return $this->nivelEducativo;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function estaAbierto(): bool {
        return $this->estado === "ABIERTO";
    }

    public function estaCerrado(): bool {
        return $this->estado === "CERRADO";
    }

    public function crear(): bool {
        $exito = $this->repository->crearProceso($this);
        if (!$exito) {
            return false;
        }

        return true;
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
        return $this->actividadRepo->listarActividades($this->id);
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
    
    public function agregarActividad(Actividad $actividad): bool {
        if ($actividad->existe()) {
            return $this->actividadRepo->actualizarActividad($actividad);
        }
        return $this->actividadRepo->agregarActividad($this->id, $actividad);
    }

    public function actualizarActividad(Actividad $actividad): bool {
        return $this->actividadRepo->actualizarActividad($actividad);
    }

    public function quitarActividad(Actividad $actividad): bool {
        return $this->actividadRepo->eliminarActividad($actividad->getId());
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

    public function getPrograma(int $programaID): ProgramaProceso {
        return $this->repository->buscarProgramaPorProceso($this->id, $programaID);
    } 

    public function agregarDocumento(ProcesoDocumento $documento): bool {
        return $this->documentoRepo->crear($documento);
    }

    public function quitarDocumento(ProcesoDocumento $documento): bool {
        return $this->documentoRepo->eliminar($documento->getId());
    }

    public function getDocumentos(): array {
        return $this->documentoRepo->listarDocumentosPorProceso($this->id);
    }

    public function getDocumento(int $documentoID): ProcesoDocumento {
        return $this->documentoRepo->buscarPorID($documentoID);
    }

    public function getNotificacionesEnviadas(): array {
        return $this->repository->listarNotificacionesEnviadas($this->id);
    }

    public function toDTO(): ProcesoDTO {
        return new ProcesoDTO(
            $this->id,
            $this->nombre,
            $this->nivelEducativo,
            $this->estado
        );
    }
}