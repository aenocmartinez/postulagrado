<?php

namespace Src\application\usecase\notificaciones;

use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class AnularNotificacionUseCase
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

        if (strtoupper($notificacion->getEstado()) !== 'PROGRAMADA') {
            return new ResponsePostulaGrado(400, 'Solo se pueden anular notificaciones en estado PROGRAMADA.');
        }

        $notificacion->setEstado('ANULADA');

        $resultado = $notificacion->actualizar();

        if (!$resultado) {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al anular la notificación.');
        }

        return new ResponsePostulaGrado(200, 'La notificación se ha anulado exitosamente.', $notificacion);
    }
}
