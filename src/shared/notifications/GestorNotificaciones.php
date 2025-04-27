<?php

namespace Src\Shared\Notifications;

class GestorNotificaciones
{
    /**
     * Disparar una notificación al medio correspondiente usando el DTO.
     *
     * @param Notificacion $notificacion
     * @param NotificacionDTO $notificacionDTO
     * @return bool
     */
    public function disparar(Notificacion $notificacion, NotificacionDTO $notificacionDTO): bool
    {
        return $notificacion->enviar($notificacionDTO);
    }

    /**
     * Obtener la clase correspondiente al canal de notificación.
     *
     * @param string $canal
     * @return Notificacion|null
     */
    private function getNotificacionByCanal(string $canal): ?Notificacion
    {
        $canales = [            
            'mailtrap' => NotificacionEmailMailtrap::class, 
        ];

        if (array_key_exists($canal, $canales)) {

            return new $canales[$canal]();
        }

        return null;
    }

    /**
     * Enviar notificación a través de los canales especificados.
     *
     * @param NotificacionDTO $notificacionDTO
     * @return bool
     */
    public function enviarNotificacion(NotificacionDTO $notificacionDTO): bool
    {
        $canales = $notificacionDTO->getCanales(); // Obtener los canales de notificación

        foreach ($canales as $canal) {
            
            $notificacion = $this->getNotificacionByCanal($canal);

            if ($notificacion) {
                
                if (!$this->disparar($notificacion, $notificacionDTO)) {
                    return false; // Si algún canal falla, retornamos false
                }

            } else {
                return false;
            }
        }

        return true; 
    }
}
