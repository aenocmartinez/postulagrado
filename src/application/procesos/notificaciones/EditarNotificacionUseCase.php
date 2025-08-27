<?php

namespace Src\application\procesos\notificaciones;

use Src\application\procesos\notificaciones\DTO\FormularioEdicionNotificacionDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class EditarNotificacionUseCase
{
    public function __construct(
        private NotificacionRepository $notificacionRepo,
        private ContactoRepository $contactoRepo
    ) {}

    public function ejecutar(int $notificacionID): ResponsePostulaGrado
    {
        $notificacion = $this->notificacionRepo->buscarPorID($notificacionID);
        if (!$notificacion->existe()) {
            return new ResponsePostulaGrado(404, 'La notificación no fue encontrada.');
        }

        $formularioNotificacionDTO = new FormularioEdicionNotificacionDTO();
        $formularioNotificacionDTO->notificacionID  = $notificacion->getID();
        $formularioNotificacionDTO->procesoID       = $notificacion->getProceso()->getId();
        $formularioNotificacionDTO->procesoNombre   = $notificacion->getProceso()->getNombre();
        $formularioNotificacionDTO->asunto          = $notificacion->getAsunto();
        $formularioNotificacionDTO->mensaje         = $notificacion->getMensaje();
        $formularioNotificacionDTO->destinatarios   = $notificacion->getDestinatarios();
        $formularioNotificacionDTO->fechaEnvio      = $notificacion->getFechaCreacion();
        $formularioNotificacionDTO->canal           = $notificacion->getCanal();
        $formularioNotificacionDTO->contactos       = $this->contactoRepo->listar();

        return new ResponsePostulaGrado(200, 'Notificación encontrada.', $formularioNotificacionDTO);
    }
}
