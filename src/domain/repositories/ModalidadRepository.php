<?php

namespace Src\domain\repositories;

use Src\domain\Modalidad;

interface ModalidadRepository {
    
    public function BuscarPorID(int $modalidadID): Modalidad;
    public function Listar(): array;
}