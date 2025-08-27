<?php

namespace Src\infrastructure\controller\seguimiento;

use Src\application\seguimiento\ConsultarSeguimientoProcesoUseCase;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Shared\Notifications\Notificacion;
use Src\shared\response\ResponsePostulaGrado;

class ProcesoSeguimientoController
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ProgramaRepository $programaRepo,
        private ActividadRepository $actividadRepo,
        private NotificacionRepository $notificacionRepo
    ) {}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        return (new ConsultarSeguimientoProcesoUseCase(
                $this->procesoRepo,
                $this->programaRepo,
                $this->actividadRepo,
                $this->notificacionRepo
            ))->ejecutar($procesoID);         
    }
}