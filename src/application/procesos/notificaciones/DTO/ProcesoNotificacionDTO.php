<?php

namespace Src\application\procesos\notificaciones\DTO;

class ProcesoNotificacionDTO
{
    public int $procesoID;
    public string $procesoNombre;
    public array $notificaciones = []; 
}