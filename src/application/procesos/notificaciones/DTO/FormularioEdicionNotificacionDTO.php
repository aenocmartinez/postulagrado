<?php

namespace Src\application\procesos\notificaciones\DTO;

class FormularioEdicionNotificacionDTO
{
    public int    $notificacionID;
    public int    $procesoID;
    public string $procesoNombre;
    public string $asunto;
    public string $mensaje;
    public string $destinatarios;
    public string $fechaEnvio;
    public string $canal;
    public array  $contactos = [];

}
