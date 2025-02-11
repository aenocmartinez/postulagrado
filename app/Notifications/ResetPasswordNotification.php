<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Crear una nueva instancia de notificación.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Definir los canales por los que se enviará la notificación.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Crear el mensaje de correo personalizado.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔒 Recuperación de Contraseña - PostulaGrado')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Hemos recibido una solicitud para restablecer tu contraseña en PostulaGrado.')
            ->line('Si no hiciste esta solicitud, puedes ignorar este mensaje.')
            ->action('Restablecer Contraseña', url(route('password.reset', $this->token, false)))
            ->line('Este enlace expirará en 60 minutos por razones de seguridad.')
            ->line('Si necesitas ayuda, puedes contactarnos en soporte@postulagrado.com.')
            ->salutation('Saludos, el equipo de PostulaGrado.');
    }

    /**
     * Obtener la representación en array de la notificación.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reset_url' => url(route('password.reset', $this->token, false))
        ];
    }
}
