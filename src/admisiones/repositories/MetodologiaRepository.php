<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Metodologia;

interface MetodologiaRepository {
    
    public function BuscarPorID(int $modalidadID): Metodologia;
    public function Listar(): array;
}