<?php

namespace Src\admisiones\procesos\repositories;

use Src\admisiones\procesos\domain\Proceso;

interface ProcesoRepository {
    public static function listarProcesos(): array;
    public static function buscarProcesoPorId(int $id): Proceso;
    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, string $nivelEducativo): Proceso;
    public function crearProceso(Proceso $proceso): bool;
}