<?php

namespace Src\domain\proceso\actividad\service;

use Carbon\CarbonImmutable;

/**
 * Clasifica actividades por estado temporal usando sus VOs
 * - RangoFechasActividad expone: inicio(): \DateTimeImmutable, fin(): \DateTimeImmutable
 * - Actividad expone: rango(): RangoFechasActividad
 */
final class ClasificarActividadesPorEstadoTemporal
{
    /**
     * @param iterable<\Src\domain\proceso\actividad\Actividad> $actividades
     * @param CarbonImmutable|null $ahora  Punto de referencia (default: now('America/Bogota'))
     * @return array{
     *   EnCurso: array,
     *   Finalizadas: array,
     *   Programadas: array,
     *   ProximasIniciar: array
     * }
     */
    public static function ejecutar(iterable $actividades, ?CarbonImmutable $ahora = null): array
    {
        $ahora ??= CarbonImmutable::now('America/Bogota');

        $res = [
            'EnCurso'        => [],
            'Finalizadas'    => [],
            'Programadas'    => [],
            'ProximasIniciar'=> [],
        ];

        foreach ($actividades as $actividad) {
            // Tomamos las fechas desde el VO de rango
            $rango = $actividad->rango(); // -> RangoFechasActividad
            $ini   = CarbonImmutable::instance($rango->inicio())->tz('America/Bogota');
            $fin   = CarbonImmutable::instance($rango->fin())->tz('America/Bogota');

            // Inclusivo en bordes: [inicio, fin]
            if ($ini->lte($ahora) && $fin->gte($ahora)) {
                $res['EnCurso'][] = $actividad;
                continue;
            }

            if ($fin->lt($ahora)) {
                $res['Finalizadas'][] = $actividad;
                continue;
            }

            // ini > ahora → futura: distinguir próximas (<= 7 días) vs programadas
            $diasParaIniciar = $ahora->diffInDays($ini); // entero, redondeo hacia abajo
            if ($diasParaIniciar <= 7) {
                $res['ProximasIniciar'][] = $actividad;
            } else {
                $res['Programadas'][] = $actividad;
            }
        }

        return $res;
    }
}
