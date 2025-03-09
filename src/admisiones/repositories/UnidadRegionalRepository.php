<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\UnidadRegional;

interface UnidadRegionalRepository {
    
    public function BuscarPorID(int $modalidadID): UnidadRegional;
    public function Listar(): array;
}