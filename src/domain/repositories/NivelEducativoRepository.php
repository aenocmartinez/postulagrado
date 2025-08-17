<?php

namespace Src\domain\repositories;

use Src\domain\NivelEducativo;

interface NivelEducativoRepository {
    
    public function BuscarPorID(int $nivelEducativoID): NivelEducativo;
    public function Listar(): array;
}