<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\dao\mysql\ProcesoDao;

class ListarProcesosUseCase
{
    public static function ejecutar(): array
    {
        return ProcesoDao::listarProcesos();
    }
}