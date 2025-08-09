<?php

namespace Src\UseCase\Notificaciones;

use Illuminate\Support\Facades\Log;
use Src\Shared\Notifications\NotificacionDTO;
use Src\Shared\Notifications\GestorNotificaciones;
use Src\domain\Notificacion;

class EnviarNotificacionUseCase
{
    protected GestorNotificaciones $gestorNotificaciones;

    public function __construct()
    {
        $this->gestorNotificaciones = new GestorNotificaciones();
    }

    /**
     * Ejecutar el envío de una notificación.
     *
     * @param Notificacion $notificacion
     * @return bool
     */
    public function ejecutar(Notificacion $notificacion): bool
    {
        try {

            $notificacionDTO = new NotificacionDTO(
                $notificacion->getAsunto(),
                $notificacion->getMensaje(),
                explode(',', $notificacion->getDestinatarios()), 
                ['mailtrap'] 
            );

            $this->gestorNotificaciones->enviarNotificacion($notificacionDTO);


            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación ID {$notificacion->getId()}: " . $e->getMessage());
            return false;
        }
    }
}
