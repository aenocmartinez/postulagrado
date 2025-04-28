<?php

namespace Src\admisiones\dto\notificacion;

class NotificacionDTO
{
    private int $id;
    private string $asunto;
    private string $mensaje;
    private string $fechaCreacion;
    private string $canal;
    private string $destinatarios;

    public function __construct()
    {
        $this->id = 0;
        $this->asunto = '';
        $this->mensaje = '';
        $this->fechaCreacion = '';
        $this->canal = '';
        $this->destinatarios = '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAsunto(): string
    {
        return $this->asunto;
    }

    public function setAsunto(string $asunto): void
    {
        $this->asunto = $asunto;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(string $fechaCreacion): void
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getCanal(): string
    {
        return $this->canal;
    }

    public function setCanal(string $canal): void
    {
        $this->canal = $canal;
    }

    public function getDestinatarios(): string
    {
        return $this->destinatarios;
    }

    public function setDestinatarios(string $destinatarios): void
    {
        $this->destinatarios = $destinatarios;
    }
}
