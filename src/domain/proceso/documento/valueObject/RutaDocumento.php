<?php

namespace Src\domain\proceso\documento\valueObject;

final class RutaDocumento
{
    private string $value;

    public function __construct(string $ruta)
    {
        $ruta = trim($ruta);

        if ($ruta === '') {
            throw new \InvalidArgumentException('La ruta del documento no puede estar vacía.');
        }

        if (mb_strlen($ruta) > 255) {
            throw new \InvalidArgumentException('La ruta del documento supera el límite de 255 caracteres.');
        }

        if (preg_match('/[\x00-\x1F]/u', $ruta)) {
            throw new \InvalidArgumentException('La ruta contiene caracteres no válidos.');
        }

        $isHttpUrl = false;

        if (preg_match('/^https?:\/\//i', $ruta)) {
            $host = parse_url($ruta, PHP_URL_HOST);

            if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
                $isHttpUrl = true;
            } elseif (filter_var($ruta, FILTER_VALIDATE_URL)) {
                $isHttpUrl = true;
            }
        }

        if (!$isHttpUrl) {
            if (preg_match('#(^|/)\.\.(/|$)#', $ruta)) {
                throw new \InvalidArgumentException('La ruta no puede contener secuencias de subida de directorio (..).');
            }

            if (!preg_match('#^[A-Za-z0-9_\-./]+$#', $ruta)) {
                throw new \InvalidArgumentException('La ruta contiene caracteres inválidos.');
            }
        }

        $this->value = $ruta;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isUrl(): bool
    {
        return (bool) preg_match('/^https?:\/\//i', $this->value);
    }

    public function filename(): string
    {
        return basename(parse_url($this->value, PHP_URL_PATH) ?: $this->value);
    }

    public function extension(): ?string
    {
        $name = $this->filename();
        $pos = strrpos($name, '.');
        return $pos !== false ? strtolower(substr($name, $pos + 1)) : null;
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
