<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Notificacion;

interface NotificacionRepository
{
    public function buscarPorID(int $id): Notificacion;
    public function listar(): array;
    public function crear(Notificacion $notificacion): bool;
    public function actualizar(Notificacion $notificacion): bool;
    public function listarPorUsuario(string $email): array;
    public function marcarComoLeida(int $notificacionID, string $emailUsuario): bool;
}