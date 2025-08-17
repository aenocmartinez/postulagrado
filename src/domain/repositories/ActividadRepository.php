<?php

namespace Src\domain\repositories;

use Src\domain\proceso\actividad\Actividad;

interface ActividadRepository {
    public static function listarActividades(int $procesoID): array;
    public static function agregarActividad(int $procesoID, Actividad $actividad): bool;
    public static function eliminarActividad(int $actividadID): bool;
    public static function buscarActividadPorId(int $actividadID): Actividad;
    public static function actualizarActividad(Actividad $actividad): bool;
}