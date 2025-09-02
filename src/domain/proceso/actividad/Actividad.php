<?php
declare(strict_types=1);

namespace Src\domain\proceso\actividad;

use LogicException;
use DateTimeImmutable;
use Src\domain\proceso\actividad\valueObject\ActividadId;
use Src\domain\proceso\actividad\valueObject\DescripcionActividad;
use Src\domain\proceso\actividad\valueObject\RangoFechasActividad;

final class Actividad
{
    public function __construct(
        private ActividadId $id,
        private DescripcionActividad $descripcion,
        private RangoFechasActividad $rango
    ) {}

    public static function nueva(DescripcionActividad $descripcion, RangoFechasActividad $rango): self
    {
        return new self(ActividadId::none(), $descripcion, $rango);
    }

    public function asignarId(ActividadId $id): void
    {
        if ($this->id->exists()) {
            throw new LogicException('El ID de la actividad ya fue asignado.');
        }
        $this->id = $id;
    }   

    public function id(): ActividadId 
    { 
        return $this->id;
    }
    
    public function descripcion(): DescripcionActividad 
    { 
        return $this->descripcion; 
    }

    public function rango(): RangoFechasActividad 
    { 
        return $this->rango; 
    }

    public function existe(): bool 
    { 
        return $this->id->exists(); 
    }

    public function inicio(): DateTimeImmutable 
    { 
        return $this->rango->inicio(); 
    }

    public function fin(): DateTimeImmutable 
    { 
        return $this->rango->fin(); 
    }

    public function inicioStr(string $format = 'Y-m-d'): string 
    { 
        return $this->rango->inicioStr($format); 
    }

    public function finStr(string $format = 'Y-m-d'): string 
    { 
        return $this->rango->finStr($format); 
    }

    public function renombrar(DescripcionActividad $nueva): void
    {
        $this->descripcion = $nueva;
    }

    public function reprogramar(RangoFechasActividad $nuevoRango): void
    {
        $this->rango = $nuevoRango;
    }

    public static function vacia(): self
    {
        return new self(
            ActividadId::none(),
            new DescripcionActividad('Sin descripción'),
            new RangoFechasActividad('1970-01-01', '1970-01-01')
        );
    }

}
