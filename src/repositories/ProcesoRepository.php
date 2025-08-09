<?php

namespace Src\repositories;

use Src\domain\NivelEducativo;
use Src\domain\Proceso;
use Src\domain\ProgramaProceso;

interface ProcesoRepository {
    public static function listarProcesos(): array;
    public static function buscarProcesoPorId(int $id): Proceso;
    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, NivelEducativo $nivelEducativo): Proceso;
    public static function tieneActividades(int $procesoID): bool;
    public function crearProceso(Proceso $proceso): bool;
    public function eliminarProceso(int $procesoID): bool;
    public function actualizarProceso(Proceso $proceso): bool;
    public function agregarPrograma(int $procesoID, int $programaID): bool;
    public function quitarPrograma(int $procesoID, int $programaID): bool;
    public function quitarTodosLosPrograma(int $procesoID): bool;
    public function listarProgramas(int $procesoID): array;
    public function buscarProgramaPorProceso(int $procesoID, int $programaID): ProgramaProceso;
    public function listarNotificaciones(int $procesoID): array;   
    public function agregarCandidatoAProceso(int $programaProcesoID, int $codigoEstudiante, int $anio, int $periodo): bool;
    public function listarCandidatosPorProcesoYPrograma(int $procesoId, int $programaID): array;
}