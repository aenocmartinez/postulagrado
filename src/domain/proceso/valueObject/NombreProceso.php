<?php

namespace Src\domain\proceso\valueObject;

use InvalidArgumentException;

final class NombreProceso
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('El nombre del proceso no puede estar vacío.');
        }

        if (mb_strlen($value) > 150) {
            throw new InvalidArgumentException('El nombre del proceso no puede superar los 150 caracteres.');
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