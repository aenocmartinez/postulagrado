<?php

namespace Src\domain\proceso\actividad\valueObject;

use InvalidArgumentException;

final class ActividadId
{
    private ?int $value;

    public function __construct(?int $value) 
    { 
        $this->value = $value; 
    }

    public static function fromInt(int $value): self
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('El id de la actividad debe ser mayor a cero.');
        }
        return new self($value);
    }

    public static function none(): self
    {
        return new self(null);
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function exists(): bool
    {
        return $this->value > 0;
    }
}
