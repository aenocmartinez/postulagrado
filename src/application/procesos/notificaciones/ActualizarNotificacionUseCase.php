<?php

namespace Src\application\procesos\notificaciones;

use Carbon\Carbon;
use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;


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

        /** @var \Src\domain\Notificacion $notificacion */
        $notificacion = $this->notificacionRepo->buscarPorId($notificacionDTO->id);
        if (!$notificacion->existe()) {
            return new ResponsePostulaGrado(404, 'La notificación no existe.');
        }

        /** @var \Src\domain\proceso\Proceso $proceso */
        $proceso = $this->procesoRepo->buscarProcesoPorId($notificacionDTO->procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'El proceso no existe.');
        }

        if ($notificacion->getEstado() != 'PROGRAMADA') {
            return new ResponsePostulaGrado(400, 'La notificación solo puede ser actualizada si está en estado PROGRAMADA.');
        }

        $enviarNotifiacionHoy = false;

        $notificacion->setId($notificacionDTO->id);
        $notificacion->setAsunto($notificacionDTO->asunto);
        $notificacion->setMensaje($notificacionDTO->mensaje);
        $notificacion->setCanal($notificacionDTO->canal);
        $notificacion->setFechaCreacion($notificacionDTO->fechaEnvio);
        $notificacion->setDestinatarios($notificacionDTO->destinatarios);
        $notificacion->setEstado('PROGRAMADA');
        $notificacion->setProceso($proceso);
        
        if (Carbon::parse($notificacion->getFechaCreacion())->isToday()) {
            $notificacion->setEstado('ENVIADA');
            $enviarNotifiacionHoy = true;
        }
                

        $resultado = $this->notificacionRepo->actualizar($notificacion);
        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al crear la notificación.');
        }


        if ($enviarNotifiacionHoy) 
        {
            // (new EnviarNotificacionUseCase())->ejecutar($notificacion);
        }

        return new ResponsePostulaGrado(200, 'La notificación se ha actualizado exitosamente.');
    }
}
