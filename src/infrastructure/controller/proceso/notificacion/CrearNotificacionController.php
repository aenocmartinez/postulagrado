<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\CrearNotificacionUseCase;
use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearNotificacionController
{
    public function __construct(
        private NotificacionRepository $notificacionRepo,
        private ProcesoRepository $procesoRepo,
        private ContactoRepository $contactoRepo
    ) {
    }

    public function __invoke(NotificacionDTO $notificacionDTO): ResponsePostulaGrado
    {

        return (new CrearNotificacionUseCase($this->notificacionRepo, $this->procesoRepo, $this->contactoRepo))
                ->ejecutar($notificacionDTO);
    }
}