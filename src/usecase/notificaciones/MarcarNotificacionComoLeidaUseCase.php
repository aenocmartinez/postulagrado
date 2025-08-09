<?php

namespace Src\usecase\notificaciones;

use Src\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class MarcarNotificacionComoLeidaUseCase
{
    private NotificacionRepository $notificacionRepository;

    public function __construct(NotificacionRepository $notificacionRepository)
    {
        $this->notificacionRepository = $notificacionRepository;
    }

    public function ejecutar(int $notificacionID, string $emailUsuario): ResponsePostulaGrado
    {
        $this->notificacionRepository->marcarComoLeida($notificacionID, $emailUsuario);

        return new ResponsePostulaGrado(200, 'Notificación encontrada.');
    }    
}