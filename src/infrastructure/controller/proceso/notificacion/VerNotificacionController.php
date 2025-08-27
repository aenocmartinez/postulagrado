<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class VerNotificacionController
{
    public function __construct(private NotificacionRepository $notificacionRepo)
    {
    }

    public function __invoke(int $notificacionID): ResponsePostulaGrado
    {
        $notificacion = $this->notificacionRepo->buscarPorId($notificacionID);

        if (!$notificacion->existe()) {
            return new ResponsePostulaGrado(404, 'Notificación no encontrada.');
        }

        $notificacionDTO = new NotificacionDTO();
        $notificacionDTO->id             = $notificacion->getId();
        $notificacionDTO->asunto         = $notificacion->getAsunto();
        $notificacionDTO->mensaje        = $notificacion->getMensaje();
        $notificacionDTO->canal          = $notificacion->getCanal();
        $notificacionDTO->destinatarios  = $notificacion->getDestinatarios();
        $notificacionDTO->fechaEnvio     = $notificacion->getFechaCreacion();
        $notificacionDTO->procesoID      = $notificacion->getProceso()->getId();

        return new ResponsePostulaGrado(200, 'Notificación encontrada.', $notificacionDTO);
    }
}