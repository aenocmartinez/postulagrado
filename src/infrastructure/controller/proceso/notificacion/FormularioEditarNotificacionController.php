<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\EditarNotificacionUseCase;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\Shared\Notifications\Notificacion;
use Src\shared\response\ResponsePostulaGrado;

class FormularioEditarNotificacionController
{
    public function __construct(
        private NotificacionRepository $notificacionRepo,
        private ContactoRepository $contactoRepo
    ){}

    public function __invoke(int $notificacionID): ResponsePostulaGrado
    {
        return (new EditarNotificacionUseCase($this->notificacionRepo, $this->contactoRepo))->ejecutar($notificacionID);
    }
}