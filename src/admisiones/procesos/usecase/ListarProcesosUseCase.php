<?php

namespace Src\admisiones\procesos\usecase;

use Src\admisiones\procesos\dao\mysql\ProcesoDao;

class ListarProcesosUseCase
{
    public static function ejecutar(): array
    {
        return ProcesoDao::listarProcesos();
    }
}