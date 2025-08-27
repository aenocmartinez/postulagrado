<?php

namespace Src\application\procesos\notificaciones;

use Carbon\Carbon;
use Src\application\procesos\notificaciones\DTO\NotificacionDTO;
use Src\domain\Notificacion;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearNotificacionUseCase
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
        $proceso = $this->procesoRepo->buscarProcesoPorId($notificacionDTO->procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'El proceso no existe.');
        }

        $enviarNotifiacionHoy = false;

        $notificacion = new Notificacion();
        $notificacion->setAsunto($notificacionDTO->asunto);
        $notificacion->setMensaje($notificacionDTO->mensaje);
        $notificacion->setFechaCreacion($notificacionDTO->fechaEnvio);
        $notificacion->setCanal($notificacionDTO->canal);
        $notificacion->setDestinatarios($notificacionDTO->destinatarios);
        $notificacion->setEstado('PROGRAMADA');
        $notificacion->setProceso($proceso);
        
        if (Carbon::parse($notificacion->getFechaCreacion())->isToday()) {
            $notificacion->setEstado('ENVIADA');
            $enviarNotifiacionHoy = true;
        }
        
        $resultado = $this->notificacionRepo->crear($notificacion);
        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al crear la notificación.');
        }


        // if ($enviarNotifiacionHoy) 
        // {
        //     (new EnviarNotificacionUseCase())->ejecutar($notificacion);
        // }

        return new ResponsePostulaGrado(201, 'La notificación se ha creado exitosamente.');
    }
}
