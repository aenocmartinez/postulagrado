<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\NivelEducativo;

interface NivelEducativoRepository {
    
    public function BuscarPorID(int $nivelEducativoID): NivelEducativo;
    public function Listar(): array;
}