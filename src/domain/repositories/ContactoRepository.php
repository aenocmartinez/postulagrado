<?php

namespace Src\domain\repositories;

use Src\domain\programa\contacto\Contacto;

interface ContactoRepository {
    public function listar(): array;
    public function buscarPorID(int $id): Contacto;
    public function crear(Contacto $contacto): bool;
    public function actualizar(Contacto $contacto): bool;
    public function eliminar(int $programaContactoID): bool;
    public function buscarPorProgramaID(int $programaID): Contacto;
}