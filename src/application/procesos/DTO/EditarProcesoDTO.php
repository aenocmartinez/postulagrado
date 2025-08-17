<?php

namespace Src\Application\procesos\DTO;

final class EditarProcesoDTO {
    
    public function __construct(
        public int $id,
        public string $nombre,
        public string $nivelEducativoID,
        public string $estado = '',
        public array $nivelesEducativos = []
    ) {}
}
