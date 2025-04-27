<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Actividad;
use Src\admisiones\domain\Calendario;

interface ActividadRepository {
    public static function crearCalendario(Calendario $calendario);
    public static function listarActividades(int $procesoID): array;
    public static function agregarActividad(int $procesoID, Actividad $actividad): bool;
    public static function eliminarActividad(int $actividadID): bool;
    public static function buscarActividadPorId(int $actividadID): Actividad;
    public static function actualizarActividad(Actividad $actividad): bool;
}