<?php

namespace Src\Shared\Notifications;

class NotificacionDTO
{
    private $asunto;
    private $mensaje;
    private $destinatarios;
    private $canales; 

    public function __construct(string $asunto, string $mensaje, array $destinatarios, array $canales)
    {
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->destinatarios = $destinatarios;
        $this->canales = $canales; 
    }

    public function getAsunto(): string
    {
        return $this->asunto;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function getDestinatarios(): array
    {
        return $this->destinatarios;
    }

    public function getCanales(): array
    {
        return $this->canales; 
    }
}
