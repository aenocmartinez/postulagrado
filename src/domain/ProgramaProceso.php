<?php

namespace Src\domain;

use Src\domain\proceso\Proceso;
use Src\domain\programa\Programa;

class ProgramaProceso
{
    private Programa $programa;
    private Proceso $proceso;
    private int $porcentajeAvance;

    public function __construct(private int $id = 0) {
        $this->porcentajeAvance = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPrograma(): Programa
    {
        return $this->programa;
    }

    public function setPrograma(Programa $programa): void
    {
        $this->programa = $programa;
    }

    public function getProceso(): Proceso
    {
        return $this->proceso;
    }

    public function setProceso(Proceso $proceso): void
    {
        $this->proceso = $proceso;
    }

    public function setPorcentajeAvance(int $porcentajeAvance): void {
        $this->porcentajeAvance = $porcentajeAvance;
    }

    public function getPorcentajeAvance(): int {
        return $this->porcentajeAvance;
    }  

    public function getCandidatosAGrado(): array {
        return $this->proceso->getEstudiantesAsociados($this->programa->getId());
    }
    
    public function existe(): bool {
        return $this->id > 0;
    }
}
