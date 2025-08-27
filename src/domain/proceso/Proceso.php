<?php

namespace Src\domain\proceso;

use Src\domain\proceso\valueObject\EstadoProceso;
use Src\domain\proceso\valueObject\NivelEducativoId;
use Src\domain\proceso\valueObject\NombreProceso;
use Src\domain\proceso\valueObject\ProcesoId;

class Proceso 
{   
    private ProcesoId $procesoID;
    private NombreProceso $nombreProceso;
    private EstadoProceso $estadoProceso;
    private NivelEducativoId $nivelEducativoID;
    private string $nivelEducativoNombre;

    
    /** @var Actividades[] $actividades */
    private $actividades = [];
    
    public function __construct(
        private string $estado = "ABIERTO"
    ) {
        $this->procesoID = ProcesoId::none();
    }


    public function setId(int $id): void {
        $this->procesoID = new ProcesoId($id);
    }

    public function getId(): int {
        return $this->procesoID->value();
    }

    public function setNombre(string $nombre): void {
        $this->nombreProceso = new NombreProceso($nombre);
    }

    public function getNombre(): string {        
        return $this->nombreProceso->value();
    }

    public function setNivelEducativoID(int $nivelEducativoID): void {
        $this->nivelEducativoID = new NivelEducativoId($nivelEducativoID);
    }

    public function setNivelEducativoNombre(string $nombre): void {
        $this->nivelEducativoNombre = $nombre;
    }

    public function getNivelEducativoID(): int {
        return $this->nivelEducativoID->value();
    }

    public function getNivelEducativoNombre(): string {
        return $this->nivelEducativoNombre;
    }

    public function setEstado(string $estado): void {
        $this->estadoProceso = new EstadoProceso($estado);
    }

    public function getEstado(): string {
        return $this->estadoProceso->value();
    }

    public function estaAbierto(): bool {
        return $this->estadoProceso->esAbierto();
    }

    public function estaCerrado(): bool {
        return $this->estadoProceso->esCerrado();
    } 

    public function existe(): bool {        
        return $this->procesoID->exists();
    }

    // public function getActividadesPorEstadoTemporal(): array 
    // {
    //     $actividadesClasificadas = [
    //         'EnCurso' => [],
    //         'Finalizadas' => [],
    //         'Programadas' => [],
    //         'ProximasIniciar' => []
    //     ];

    //     $fechaActual = Carbon::now();

    //     foreach ($this->getActividades() as $actividad) 
    //     {
    //         $fechaInicio = FormatoFecha::ConvertirStringAObjetoCarbon($actividad->getFechaInicio());

    //         $fechaFin = FormatoFecha::ConvertirStringAObjetoCarbon($actividad->getFechaFin());

    //         if ($fechaInicio->lessThanOrEqualTo($fechaActual) && $fechaFin->greaterThanOrEqualTo($fechaActual)) 
    //         {
    //             $actividadesClasificadas['EnCurso'][] = $actividad;
    //         } 
    //         elseif ($fechaFin->lessThan($fechaActual)) 
    //         {
    //             $actividadesClasificadas['Finalizadas'][] = $actividad;
    //         } 
    //         elseif ($fechaInicio->greaterThan($fechaActual)) 
    //         {
    //             $diasParaIniciar = $fechaInicio->greaterThan($fechaActual) ? $fechaActual->diffInDays($fechaInicio) : 0;

    //             if ($diasParaIniciar <= 7) 
    //             {
    //                 $actividadesClasificadas['ProximasIniciar'][] = $actividad;
    //             } 
    //             else 
    //             {
    //                 $actividadesClasificadas['Programadas'][] = $actividad;
    //             }
    //         }
    //     }

    //     return $actividadesClasificadas;
    // }

    // public function agregarPrograma(Programa $programa): bool {
    //     return $this->repository->agregarPrograma($this->id, $programa->getId());
    // }

    // public function quitarPrograma(Programa $programa): bool {
    //     return $this->repository->quitarPrograma($this->id, $programa->getId());
    // }

    // public function getProgramas(): array {
    //     return $this->repository->listarProgramas($this->id);
    // }

    // public function getPrograma(int $programaID): ProgramaProceso {
    //     return $this->repository->buscarProgramaPorProceso($this->id, $programaID);
    // } 

    // public function agregarDocumento(ProcesoDocumento $documento): bool {
    //     return $this->documentoRepo->crear($documento);
    // }

    // public function quitarDocumento(ProcesoDocumento $documento): bool {
    //     return $this->documentoRepo->eliminar($documento->getId());
    // }

    // public function getDocumentos(): array {
    //     return $this->documentoRepo->listarDocumentosPorProceso($this->id);
    // }

    // public function getDocumento(int $documentoID): ProcesoDocumento {
    //     return $this->documentoRepo->buscarPorID($documentoID);
    // }

    // public function getNotificaciones(): array {
    //     return $this->repository->listarNotificaciones($this->id);
    // }

    // public function getEstudiantesAsociados(int $programaID): array {
    //     return $this->repository->listarCandidatosPorProcesoYPrograma($this->id, $programaID);        
    // }

    // public function toDTO(): ProcesoDTO {
    //     return new ProcesoDTO(
    //         $this->id,
    //         $this->nombre,
    //         $this->nivelEducativo,
    //         $this->estado
    //     );
    // }
}