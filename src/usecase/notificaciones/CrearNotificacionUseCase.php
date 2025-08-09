<?php

namespace Src\usecase\notificaciones;

use Carbon\Carbon;
use Src\domain\Notificacion;
use Src\dto\notificacion\NotificacionDTO;
use Src\repositories\NotificacionRepository;
use Src\repositories\ProcesoRepository;
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
        $proceso = $this->procesoRepo->buscarProcesoPorId($notificacionDTO->getProcesoId());
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, 'El proceso no existe.');
        }

        $enviarNotifiacionHoy = false;

        $notificacion = new Notificacion($this->notificacionRepo);
        $notificacion->setId($notificacionDTO->getId());
        $notificacion->setAsunto($notificacionDTO->getAsunto());
        $notificacion->setMensaje($notificacionDTO->getMensaje());
        $notificacion->setFechaCreacion($notificacionDTO->getFechaCreacion());
        $notificacion->setCanal($notificacionDTO->getCanal());
        $notificacion->setDestinatarios($notificacionDTO->getDestinatarios());
        $notificacion->setEstado('PROGRAMADA');
        $notificacion->setProceso($proceso);
        
        if (Carbon::parse($notificacion->getFechaCreacion())->isToday()) {
            $notificacion->setEstado('ENVIADA');
            $enviarNotifiacionHoy = true;
        }
                

        $resultado = $notificacion->crear();
        if (!$resultado) 
        {
            return new ResponsePostulaGrado(500, 'Se ha producido un error al crear la notificación.');
        }


        if ($enviarNotifiacionHoy) 
        {
            (new EnviarNotificacionUseCase())->ejecutar($notificacion);
        }

        return new ResponsePostulaGrado(201, 'La notificación se ha creado exitosamente.');
    }
}
