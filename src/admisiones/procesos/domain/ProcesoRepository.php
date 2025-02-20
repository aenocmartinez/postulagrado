<?php

namespace Src\admisiones\procesos\domain;

interface ProcesoRepository {
    public static function listarProcesos(): array;
    public static function buscarProcesoPorId(int $id): Proceso;
    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, string $nivelEducativo): Proceso;
    public function crearProceso(Proceso $proceso): bool;
    public function eliminarProceso(int $procesoID): bool;
    public function actualizarProceso(Proceso $proceso): bool;
}