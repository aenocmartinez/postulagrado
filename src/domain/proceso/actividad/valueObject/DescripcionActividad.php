<?php

namespace Src\domain\proceso\actividad\valueObject;

use InvalidArgumentException;

final class DescripcionActividad
{
    private string $value;

    public function __construct(string $value, int $max = 255)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('La descripción de la actividad no puede estar vacía.');
        }
        if (mb_strlen($value) > $max) {
            throw new InvalidArgumentException("La descripción supera los {$max} caracteres.");
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
