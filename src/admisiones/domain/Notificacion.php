<?php

namespace Src\admisiones\domain;

use Src\admisiones\repositories\NotificacionRepository;

class Notificacion
{
    private int $id;
    private string $asunto;
    private string $mensaje;
    private string $fechaCreacion;
    private string $canal;
    private string $destinatarios;
    private string $estado;
    private NotificacionRepository $notificacionRepo;

    public function __construct(NotificacionRepository $notificacionRepo)
    {
        $this->id = 0;
        $this->asunto = '';
        $this->mensaje = '';
        $this->fechaCreacion = '';
        $this->canal = '';
        $this->destinatarios = '';
        $this->estado = '';
        $this->notificacionRepo = $notificacionRepo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAsunto(): string
    {
        return $this->asunto;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getCanal(): string
    {
        return $this->canal;
    }

    public function getDestinatarios(): string
    {
        return $this->destinatarios;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function setAsunto(string $asunto): void
    {
        $this->asunto = $asunto;
    }
    
    public function setMensaje(string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }

    public function setFechaCreacion(string $fechaCreacion): void
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function setCanal(string $canal): void
    {
        $this->canal = $canal;
    }

    public function setDestinatarios(string $destinatarios): void
    {
        $this->destinatarios = $destinatarios;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function listar(): array
    {
        return $this->notificacionRepo->listar();
    }

    public function crear(): bool
    {
        return $this->notificacionRepo->crear($this);
    }

    public function existe(): bool
    {
        return $this->id > 0;
    }
}
