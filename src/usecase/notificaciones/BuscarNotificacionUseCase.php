<?php

namespace Src\usecase\notificaciones;


use Src\repositories\NotificacionRepository;

use Src\shared\response\ResponsePostulaGrado;

class BuscarNotificacionUseCase
{
    private NotificacionRepository $notificacionRepo;

    public function __construct(NotificacionRepository $notificacionRepo)
    {
        $this->notificacionRepo = $notificacionRepo;
    }

    public function ejecutar(int $notificacionID): ResponsePostulaGrado
    {

        $notificacion = $this->notificacionRepo->buscarPorID($notificacionID);

        if (!$notificacion->existe()) {
            return new ResponsePostulaGrado(404, 'La notificación no fue encontrada.');
        }

        return new ResponsePostulaGrado(200, 'Notificación encontrada.', $notificacion);
    }
}
