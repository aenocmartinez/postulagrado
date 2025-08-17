<?php

namespace Src\domain\proceso\valueObject;

use InvalidArgumentException;

final class EstadoProceso
{
    private const ABIERTO = 'ABIERTO';
    private const CERRADO = 'CERRADO';

    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));

        if (!in_array($value, [self::ABIERTO, self::CERRADO], true)) {
            throw new InvalidArgumentException("Estado no válido: {$value}");
        }

        $this->value = $value;
    }

    public static function abierto(): self
    {
        return new self(self::ABIERTO);
    }

    public static function cerrado(): self
    {
        return new self(self::CERRADO);
    }

    public static function fromString(string $estado): self
    {
        return new self($estado);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function esAbierto(): bool
    {
        return $this->value === self::ABIERTO;
    }

    public function esCerrado(): bool
    {
        return $this->value === self::CERRADO;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
