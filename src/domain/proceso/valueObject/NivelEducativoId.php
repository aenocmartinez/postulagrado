<?php

namespace Src\domain\proceso\valueObject;

use InvalidArgumentException;

final class NivelEducativoId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('El id del nivel educativo debe ser mayor a cero.');
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
