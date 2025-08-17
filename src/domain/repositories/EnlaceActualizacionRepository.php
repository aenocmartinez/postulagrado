<?php

namespace Src\domain\repositories;

use Src\domain\EnlaceActualizacion;

interface EnlaceActualizacionRepository
{
    public function guardar(EnlaceActualizacion $enlace): bool;
    public function buscarPorId(int $id): ?EnlaceActualizacion;
    public function buscarPorToken(string $token): ?EnlaceActualizacion;
    public function buscarPorCodigoEstudianteYProceso(string $codigoEstudiante, int $procesoId): ?EnlaceActualizacion;
    public function marcarComoUsado(string $token): bool;
    public function eliminarExpirados(): int;
}
