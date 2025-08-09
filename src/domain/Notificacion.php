<?php

namespace Src\domain;

use Src\repositories\NotificacionRepository;
use Src\shared\di\FabricaDeRepositorios;

class Notificacion
{
    private int $id;
    private string $asunto;
    private string $mensaje;
    private string $fechaCreacion;
    private string $canal;
    private string $destinatarios;
    private string $estado;
    private bool $fueLeida;
    private Proceso $proceso;
    private NotificacionRepository $notificacionRepo;

    public function __construct()
    {
        $this->id = 0;
        $this->asunto = '';
        $this->mensaje = '';
        $this->fechaCreacion = '';
        $this->canal = '';
        $this->destinatarios = '';
        $this->estado = '';
        $this->fueLeida = false;
        $this->proceso = new Proceso();
        $this->notificacionRepo = FabricaDeRepositorios::getInstance()->getNotificacionRepository();
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

    public function getProceso(): Proceso
    {
        return $this->proceso;
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

    public function setFueLeida(bool $fueLeida): void
    {
        $this->fueLeida = $fueLeida;
    }

    public function fueLeida(): bool
    {
        return $this->fueLeida;
    }

    public function setProceso(Proceso $proceso): void
    {
        $this->proceso = $proceso;
    }

    public function estadoAnulada(): bool
    {
        return $this->estado === 'ANULADA';
    }

    public function listar(): array
    {
        return $this->notificacionRepo->listar();
    }

    public function crear(): bool
    {
        return $this->notificacionRepo->crear($this);
    }

    public function actualizar(): bool
    {
        return $this->notificacionRepo->actualizar($this);
    }

    public function existe(): bool
    {
        return $this->id > 0;
    }
}
