<?php

namespace Src\repositories;

use Src\domain\Metodologia;

interface MetodologiaRepository {
    
    public function BuscarPorID(int $modalidadID): Metodologia;
    public function Listar(): array;
}