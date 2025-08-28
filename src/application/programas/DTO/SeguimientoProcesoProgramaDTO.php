<?php

namespace Src\application\programas\DTO;

class SeguimientoProcesoProgramaDTO
{
    public int $procesoID;
    public string $procesoNombre;
    public string $procesoEstado;
    public array $notificaciones = [];
    public array $actividadesPorEstadoTemporal = [];
    public array $estudiantesCandidatos = [];
}