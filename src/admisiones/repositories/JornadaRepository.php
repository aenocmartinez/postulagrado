<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Jornada;

interface JornadaRepository {
    
    public function BuscarPorID(int $jornadaID): Jornada;
    public function Listar(): array;
}