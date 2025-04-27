<?php

namespace Src\Shared\Notifications;

class NotificacionDTO
{
    private $asunto;
    private $mensaje;
    private $destinatarios;

    public function __construct(string $asunto, string $mensaje, array $destinatarios)
    {
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->destinatarios = $destinatarios;
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
}
