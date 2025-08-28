<?php

namespace Src\infrastructure\controller\programa;

use Illuminate\Support\Facades\Auth;
use Src\application\programas\DTO\SeguimientoProcesoProgramaDTO;
use Src\domain\proceso\actividad\Actividad;
use Src\domain\proceso\actividad\service\ClasificarActividadesPorEstadoTemporal;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class SeguimientoProgramaProcesoController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private NivelEducativoRepository $nivelEducativoRepository,
        private NotificacionRepository $notificacionRepo,
        private ActividadRepository $actividadRepo
    ) {}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepository->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404,"El proceso con ID {$procesoID} no fue encontrado.");
        }

        $actividades = $this->actividadRepo->listarActividades($proceso->getId());
    

        /** @var  App\Models\User $user*/
        $user = Auth::user();
        $notificaciones = $this->notificacionRepo->listarPorUsuario($user->email);

        $seguimiento = new SeguimientoProcesoProgramaDTO();
        $seguimiento->procesoID = $proceso->getId();
        $seguimiento->procesoNombre = $proceso->getNombre();
        $seguimiento->procesoEstado = $proceso->getEstado();
        $seguimiento->notificaciones = $notificaciones;
        $seguimiento->actividadesPorEstadoTemporal = ClasificarActividadesPorEstadoTemporal::ejecutar($actividades);

        return new ResponsePostulaGrado(200, "Proceso encontrado.", $seguimiento);
    }
}