<?php

namespace Src\admisiones\usecase\notificaciones;

use Carbon\Carbon;
use Src\admisiones\dto\notificacion\NotificacionDTO;
use Src\admisiones\repositories\NotificacionRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarNotificacionUseCase
{
    private NotificacionRepository $notificacionRepo;
    private ProcesoRepository $procesoRepo;

    public function __construct(NotificacionRepository $notificacionRepo, ProcesoRepository $procesoRepo)
    {
        $this->notificacionRepo = $notificacionRepo;
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(NotificacionDTO $notificacionDTO): ResponsePostulaGrado
    {

        /** @var \Src\admisiones\domain\Notificacion $notificacion */
        $notificacion = $this->notificacionRepo->buscarPorId($notificacionDTO->getId());
        if (!$notificacion->existe()) {
            return new ResponsePostulaGrado(404, 'La notificación no existe.');
        }

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $this->procesoRepo->buscarProcesoPorId($notificacionDTO->getProcesoId());
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'El proceso no existe.');
        }

        if ($notificacion->getEstado() != 'PROGRAMADA') {
            return new ResponsePostulaGrado(400, 'La notificación solo puede ser actualizada si está en estado PROGRAMADA.');
        }

        $enviarNotifiacionHoy = false;

        $notificacion->setId($notificacionDTO->getId());
        $notificacion->setAsunto($notificacionDTO->getAsunto());
        $notificacion->setMensaje($notificacionDTO->getMensaje());
        $notificacion->setCanal($notificacionDTO->getCanal());
        $notificacion->setFechaCreacion($notificacionDTO->getFechaCreacion());
        $notificacion->setDestinatarios($notificacionDTO->getDestinatarios());
        $notificacion->setEstado('PROGRAMADA');
        $notificacion->setProceso($proceso);
        
        if (Carbon::parse($notificacion->getFechaCreacion())->isToday()) {
            $notificacion->setEstado('ENVIADA');
            $enviarNotifiacionHoy = true;
        }
                

        $resultado = $notificacion->actualizar();
        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al crear la notificación.');
        }


        if ($enviarNotifiacionHoy) 
        {
            (new EnviarNotificacionUseCase())->ejecutar($notificacion);
        }

        return new ResponsePostulaGrado(200, 'La notificación se ha actualizado exitosamente.');
    }
}
