<?php

namespace Src\domain\repositories;

use Src\domain\UnidadRegional;

interface UnidadRegionalRepository {
    
    public function BuscarPorID(int $modalidadID): UnidadRegional;
    public function Listar(): array;
}