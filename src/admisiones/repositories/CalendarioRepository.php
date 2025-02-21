<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Calendario;
use Src\admisiones\domain\Proceso;

interface CalendarioRepository {
    public static function crearCalendario(Calendario $calendario);
    public static function listarActividades(int $procesoID): array;
}