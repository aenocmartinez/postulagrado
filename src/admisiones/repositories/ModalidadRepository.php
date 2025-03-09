<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Modalidad;

interface ModalidadRepository {
    
    public function BuscarPorID(int $modalidadID): Modalidad;
    public function Listar(): array;
}