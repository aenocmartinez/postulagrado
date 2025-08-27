<?php

namespace Src\Application\procesos\DTO;

final class ProcesoIndexDTO {
    
    public function __construct(
        public int $id,
        public string $nombre,
        public string $nivelEducativoNombre,
        public string $estado
    ) {}

    public function estaCerrado(): bool {
        return $this->estado === 'CERRADO';
    }
}
