<?php

namespace Src\application\seguimiento;

use Carbon\CarbonImmutable;
use Src\application\seguimiento\DTO\ProcesoSeguimientoDTO;
use Src\domain\proceso\actividad\service\ClasificarActividadesPorEstadoTemporal;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\Shared\Notifications\Notificacion;
use Src\shared\response\ResponsePostulaGrado;

class ConsultarSeguimientoProcesoUseCase
{    
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ProgramaRepository $programaRepo,
        private ActividadRepository $actividadRepo,
        private NotificacionRepository $notificacionRepo,        
    ) {}

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        /** @var Src\domain\proceso\Proceso $proceso*/
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);        
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, "El proceso no existe");
        }

        $actividades = $this->actividadRepo->listarActividades($procesoID);
        $actividadesClasificadas = ClasificarActividadesPorEstadoTemporal::ejecutar($actividades, CarbonImmutable::now('America/Bogota'));

        $procesoSeguimientoDTO = new ProcesoSeguimientoDTO(
            procesoID: $proceso->getID(),
            nombreProceso: $proceso->getNombre(),
            nombreNivelEducativo: $proceso->getNivelEducativoNombre(),
            estadoProceso: $proceso->getEstado(),
            actividadesPorEstado: $actividadesClasificadas,
            programas: $this->programaRepo->listarProgramasPorProceso($procesoID),
            notificaciones: $this->notificacionRepo->listarNotificacionesPorProceso($procesoID),
        );

        return new ResponsePostulaGrado(200, "Proceso encontrado", $procesoSeguimientoDTO);
    }
}