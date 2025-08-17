<?php

namespace Src\domain\proceso\valueObject;

use InvalidArgumentException;

final class ProcesoId
{
    private ?int $value;

    public function __construct(?int $value)
    {
        if ($value !== null && $value <= 0) {
            throw new InvalidArgumentException('El id del proceso debe ser mayor a cero.');
        }
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
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
        return $this->value !== null;
    }
}
