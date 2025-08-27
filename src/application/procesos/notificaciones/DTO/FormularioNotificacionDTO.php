<?php

namespace Src\application\procesos\notificaciones\DTO;

class FormularioNotificacionDTO
{
    public int $notificacionID;
    public int $procesoID;
    public string $procesoNombre;
    public array $contactos = [];
}
