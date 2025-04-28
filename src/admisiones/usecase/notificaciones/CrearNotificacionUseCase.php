<?php

namespace Src\admisiones\usecase\notificaciones;

use Src\admisiones\domain\Notificacion;
use Src\admisiones\dto\notificacion\NotificacionDTO;
use Src\admisiones\repositories\NotificacionRepository;

use Src\shared\response\ResponsePostulaGrado;

class CrearNotificacionUseCase
{
    private NotificacionRepository $notificacionRepo;

    public function __construct(NotificacionRepository $notificacionRepo)
    {
        $this->notificacionRepo = $notificacionRepo;
    }

    public function ejecutar(NotificacionDTO $notificacionDTO): ResponsePostulaGrado
    {
        $notifacion = new Notificacion($this->notificacionRepo);
        $notifacion->setId($notificacionDTO->getId());
        $notifacion->setAsunto($notificacionDTO->getAsunto());
        $notifacion->setMensaje($notificacionDTO->getMensaje());
        $notifacion->setFechaCreacion($notificacionDTO->getFechaCreacion());
        $notifacion->setCanal($notificacionDTO->getCanal());
        $notifacion->setDestinatarios($notificacionDTO->getDestinatarios());


        $resultado = $notifacion->crear();
        if (!$resultado) {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al crear la notificación.');
        }

        return new ResponsePostulaGrado(201, 'La notificación se ha creado exitosamente.');

    }
}
