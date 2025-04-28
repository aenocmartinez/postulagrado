<?php

namespace Src\admisiones\usecase\notificaciones;

use Src\admisiones\repositories\NotificacionRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarNotificacionesUseCase
{
    private NotificacionRepository $notificacionRepo;

    public function __construct(NotificacionRepository $notificacionRepo)
    {
        $this->notificacionRepo = $notificacionRepo;
    }

    public function ejecutar(): ResponsePostulaGrado
    {
        return new ResponsePostulaGrado(200, "Listado de niveles", $this->notificacionRepo->listar());
    }
}