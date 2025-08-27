<?php

namespace Src\application\procesos\notificaciones\DTO;

class NotificacionDTO
{
    public int $id;
    public int $procesoID;
    public string $asunto;
    public string $mensaje;
    public string $destinatarios;
    public string $fechaEnvio;
    public string $canal;
}