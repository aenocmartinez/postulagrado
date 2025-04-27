<?php

namespace Src\Shared\Notifications;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificacionEmailMailtrap implements Notificacion
{
    /**
     * Enviar la notificación por correo electrónico usando Mailtrap.
     *
     * @param NotificacionDTO $notificacionDTO
     * @return bool
     */
    public function enviar(NotificacionDTO $notificacionDTO): bool
    {
        try {
            $destinatarios = $notificacionDTO->getDestinatarios();
            $mensaje = $notificacionDTO->getMensaje();
            $asunto = $notificacionDTO->getAsunto();

            // Enviar el correo a cada destinatario
            foreach ($destinatarios as $destinatario) {
                Mail::html($mensaje, function ($message) use ($destinatario, $asunto) {
                    $message->to($destinatario)
                            ->subject($asunto)
                            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                });
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar el correo electrónico: " . $e->getMessage());
            return false;
        }
    }
}
