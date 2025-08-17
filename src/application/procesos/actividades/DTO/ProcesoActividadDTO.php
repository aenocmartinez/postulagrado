<?php

namespace Src\application\procesos\actividades\DTO;

class ProcesoActividadDTO
{
    public int $actividadID;
    public int $procesoID;
    public string $nombreNivelEducativo;
    public string $nombreProceso;    
    public string $estadoProceso;
    public array $actividades = [];
}