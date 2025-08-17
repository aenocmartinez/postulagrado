<?php

namespace Src\domain\repositories;

use Src\domain\ProgramaContacto;

interface ProgramaContactoRepository {
    public function listar(string $criterio = ""): array;
    public function buscarPorID(int $id): ProgramaContacto;
    public function crear(ProgramaContacto $contacto): bool;
    public function actualizar(ProgramaContacto $contacto): bool;
    public function eliminar(int $programaContactoID): bool;
}