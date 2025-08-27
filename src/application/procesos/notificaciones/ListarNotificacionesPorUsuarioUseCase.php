<?php

namespace Src\application\usecase\notificaciones;

use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarNotificacionesPorUsuarioUseCase
{
    private NotificacionRepository $notificacionRepository;

    public function __construct(NotificacionRepository $notificacionRepository)
    {
        $this->notificacionRepository = $notificacionRepository;
    }

    public function ejecutar(string $emailUsuario): ResponsePostulaGrado
    {
        $notificaciones = $this->notificacionRepository->listarPorUsuario($emailUsuario);

        return new ResponsePostulaGrado(200, 'Notificación encontrada.', $notificaciones);
    }
}