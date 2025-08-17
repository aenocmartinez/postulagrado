<?php

namespace Src\domain\proceso\actividad\valueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final class RangoFechasActividad
{
    private DateTimeImmutable $inicio;
    private DateTimeImmutable $fin;

    public function __construct(string|DateTimeImmutable $inicio, string|DateTimeImmutable $fin)
    {
        $this->inicio = is_string($inicio) ? self::convertirADateTimeImmutable($inicio) : $inicio;
        $this->fin    = is_string($fin)    ? self::convertirADateTimeImmutable($fin)    : $fin;

        if ($this->inicio > $this->fin) {
            throw new InvalidArgumentException('La fecha de inicio no puede ser posterior a la fecha fin.');
        }
    }

    public function inicio(): DateTimeImmutable 
    { 
        return $this->inicio; 
    }

    public function fin(): DateTimeImmutable    
    { 
        return $this->fin; 
    }

    public function inicioStr(string $format = 'Y-m-d'): string 
    { 
        return $this->inicio->format($format); 
    }

    public function finStr(string $format = 'Y-m-d'): string    
    { 
        return $this->fin->format($format); 
    }

    private static function convertirADateTimeImmutable(string $valor): DateTimeImmutable
    {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d|H:i:s', $valor.'|00:00:00');
        if ($dt === false) {
            $dt = new DateTimeImmutable($valor);
        }
        return $dt;
    }
}

