<?php

namespace Src\domain\repositories;

use Src\domain\Jornada;

interface JornadaRepository {
    
    public function BuscarPorID(int $jornadaID): Jornada;
    public function Listar(): array;
}