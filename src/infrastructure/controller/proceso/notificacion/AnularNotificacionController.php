<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\AnularNotificacionUseCase;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class AnularNotificacionController
{
    public function __construct(
        private NotificacionRepository $notificacionRepo
    ){}

    public function __invoke(int $notificacionID): ResponsePostulaGrado
    {
        return (new AnularNotificacionUseCase($this->notificacionRepo))->ejecutar($notificacionID);
    }
}