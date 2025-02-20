<?php

namespace Src\admisiones\procesos\repositories;

use Src\admisiones\procesos\domain\Proceso;

interface ProcesoRepository {
    public static function listarProcesos(): array;
    public static function buscarProcesoPorId(int $id): Proceso;
}