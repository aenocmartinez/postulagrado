<?php

namespace Src\Shared\Notifications;

interface Notificacion
{
    /**
     * Enviar la notificación.
     *
     * @param NotificacionDTO $notificacionDTO
     * @return bool
     */
    public function enviar(NotificacionDTO $notificacionDTO): bool;
}
