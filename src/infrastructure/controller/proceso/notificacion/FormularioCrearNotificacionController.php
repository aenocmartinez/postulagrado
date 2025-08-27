<?php

namespace Src\infrastructure\controller\proceso\notificacion;

use Src\application\procesos\notificaciones\DTO\FormularioNotificacionDTO;
use Src\domain\repositories\ContactoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class FormularioCrearNotificacionController
{    
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NotificacionRepository $notificacionRepo,
        private ContactoRepository $contactoRepo
    ) {}

    public function __invoke($procesoID): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404,'El proceso no existe.');
        }

        $formularioNotificacion = new FormularioNotificacionDTO();
        $formularioNotificacion->procesoID = $proceso->getId();
        $formularioNotificacion->procesoNombre = $proceso->getNombre();
        $formularioNotificacion->contactos = $this->contactoRepo->listar();

        return new ResponsePostulaGrado(200,'Formulario para crear notificacion', $formularioNotificacion);
    }
}