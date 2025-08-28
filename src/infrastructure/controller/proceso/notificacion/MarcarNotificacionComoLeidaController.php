<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\MarcarNotificacionComoLeidaUseCase;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class MarcarNotificacionComoLeidaController
{
    public function __construct(private NotificacionRepository $notificacionRepo)
    {
    }

    public function __invoke(int $notificacionID, string $email): ResponsePostulaGrado
    {
        return (new MarcarNotificacionComoLeidaUseCase(
            $this->notificacionRepo
        ))->ejecutar($notificacionID, $email);        
    }
}