<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Proceso;

interface ProcesoRepository {
    public static function listarProcesos(): array;
    public static function buscarProcesoPorId(int $id): Proceso;
    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, string $nivelEducativo): Proceso;
    public static function tieneCalendarioConActividades(int $procesoID): bool;
    public function crearProceso(Proceso $proceso): bool;
    public function eliminarProceso(int $procesoID): bool;
    public function actualizarProceso(Proceso $proceso): bool;
    public function agregarPrograma(int $procesoID, int $programaID): bool;
    public function quitarPrograma(int $procesoID, int $programaID): bool;
    public function quitarTodosLosPrograma(int $procesoID): bool;
    public function listarProgramas(int $procesoID): array;
}