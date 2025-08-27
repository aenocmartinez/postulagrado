<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\ActualizarNotificacionUseCase;
use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarNotificacionController
{
    public function __construct(
        private NotificacionRepository $notificacionRepo,
        private ProcesoRepository $procesoRepo
    ) {}

    public function __invoke(NotificacionDTO $notificacionDTO): ResponsePostulaGrado
    {
        return (new ActualizarNotificacionUseCase(
            $this->notificacionRepo,
            $this->procesoRepo
        ))->ejecutar($notificacionDTO);
    }
}