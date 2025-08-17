<?php

namespace Src\domain\proceso\documento\valueObject;

final class NombreDocumento
{
    private string $value;

    public function __construct(string $nombre)
    {
        $nombre = trim(preg_replace('/\s+/u', ' ', $nombre ?? ''));

        if ($nombre === '') {
            throw new \InvalidArgumentException('El nombre del documento no puede estar vacío.');
        }

        if (mb_strlen($nombre) > 120) {
            throw new \InvalidArgumentException('El nombre del documento supera el límite de 120 caracteres.');
        }

        if (preg_match('/[\x00-\x1F]/u', $nombre)) {
            throw new \InvalidArgumentException('El nombre contiene caracteres de control no válidos.');
        }

        if (!preg_match('/^[\p{L}\p{M}0-9 _\-\.,()\[\]]+$/u', $nombre)) {
            throw new \InvalidArgumentException('El nombre contiene caracteres no permitidos.');
        }

        $this->value = $nombre;
    }

    public static function fromString(string $nombre): self
    {
        return new self($nombre);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function display(): string
    {
        return $this->value;
    }

    public function slug(): string
    {
        $s = mb_strtolower($this->value, 'UTF-8');
        
        $s = preg_replace('/\s+/u', '-', $s);
        
        $s = preg_replace('/[^a-z0-9\.\-]+/u', '', $s);
        
        $s = preg_replace('/\-{2,}/', '-', $s);
        return trim($s, '-');
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
