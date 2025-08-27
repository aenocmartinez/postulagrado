<?php

namespace Src\application\procesos\notificaciones;

use Src\application\procesos\notificaciones\DTO\ProcesoNotificacionDTO;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class NotificacionesDeUnProcesoUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NotificacionRepository $notificacionRepo
    ) {}

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {            
            return new ResponsePostulaGrado(404, "El proceso no existe", null);
        }

        $procesoNotificacionDTO = new ProcesoNotificacionDTO();
        $procesoNotificacionDTO->procesoID = $proceso->getId();
        $procesoNotificacionDTO->procesoNombre = $proceso->getNombre();

        foreach ($this->notificacionRepo->listarNotificacionesPorProceso($proceso->getId()) as $notificacion) {
            $procesoNotificacionDTO->notificaciones[] = [
                'id' => $notificacion->getId(),
                'canal' => $notificacion->getCanal(),
                'estado' => $notificacion->getEstado(),
                'asunto' => $notificacion->getAsunto(),
                'mensaje' => $notificacion->getMensaje(),
                'destinatarios' => $notificacion->getDestinatarios(),
                'fecha_creacion' => $notificacion->getFechaCreacion(),
            ];
            
        }

        return new ResponsePostulaGrado(200, "Notificaciones del proceso {$procesoID}", $procesoNotificacionDTO);
    }
}