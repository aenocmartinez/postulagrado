<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\NotificacionesDeUnProcesoUseCase;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class NotifiacionesDeUnProcesoController
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NotificacionRepository $notificacionRepo
    ) {}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        return (new NotificacionesDeUnProcesoUseCase($this->procesoRepo, $this->notificacionRepo))->ejecutar($procesoID);
    }
}