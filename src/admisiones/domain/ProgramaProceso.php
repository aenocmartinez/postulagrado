<?php

namespace Src\admisiones\domain;

class ProgramaProceso
{
    private Programa $programa;
    private Proceso $proceso;

    public function __construct(private int $id = 0) {}

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
}
