<?php

namespace Src\admisiones\domain;

class Calendario {
    
    private int $id;
    private Proceso $proceso;

    public function __construct()
    {
        $this->id = 0;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getId(): int {
        return $this->id;
    }
    
    public function setProceso(Proceso $proceso): void {
        $this->proceso = $proceso;
    }

    public function getProceso(): Proceso {
        return $this->proceso;
    }
}