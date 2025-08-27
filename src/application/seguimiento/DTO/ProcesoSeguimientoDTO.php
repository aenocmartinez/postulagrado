<?php

namespace Src\application\seguimiento\DTO;

class ProcesoSeguimientoDTO
{
    public function __construct(
        public int $procesoID,
        public string $nombreProceso,
        public string $nombreNivelEducativo,
        public string $estadoProceso,
        public array $programas = [],
        public array $actividadesPorEstado = [],
        public array $notificaciones = [],
    ) {}
}
