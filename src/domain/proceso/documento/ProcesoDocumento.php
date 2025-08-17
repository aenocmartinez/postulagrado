<?php

namespace Src\domain\proceso\documento;

use Src\domain\proceso\documento\valueObject\ProcesoDocumentoId;
use Src\domain\proceso\documento\valueObject\NombreDocumento;
use Src\domain\proceso\documento\valueObject\ProcesoId;
use Src\domain\proceso\documento\valueObject\RutaDocumento;

final class ProcesoDocumento
{
    private ?ProcesoDocumentoId $id;  
    private ProcesoId $procesoId;
    private NombreDocumento $nombre;
    private RutaDocumento $ruta;

    public function __construct(
        ?ProcesoDocumentoId $id,
        ProcesoId $procesoId,
        NombreDocumento $nombre,
        RutaDocumento $ruta
    ) {
        $this->id = $id;
        $this->procesoId = $procesoId;
        $this->nombre = $nombre;
        $this->ruta = $ruta;
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            isset($data['id']) && $data['id'] ? new ProcesoDocumentoId((int)$data['id']) : null,
            new ProcesoId((int)$data['proceso_id']),
            new NombreDocumento((string)$data['nombre']),
            new RutaDocumento((string)$data['ruta'])
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id'         => $this->id?->value(),
            'proceso_id' => $this->procesoId->value(),
            'nombre'     => $this->nombre->value(),
            'ruta'       => $this->ruta->value(),
        ];
    }

    public function id(): ?ProcesoDocumentoId
    {
        return $this->id;
    }

    public function procesoId(): ProcesoId
    {
        return $this->procesoId;
    }

    public function nombre(): NombreDocumento
    {
        return $this->nombre;
    }

    public function ruta(): RutaDocumento
    {
        return $this->ruta;
    }


    public function setId(ProcesoDocumentoId $id): void
    {        
        if ($this->id !== null && !$this->id->equals($id)) {
            throw new \LogicException('El ID de ProcesoDocumento ya fue establecido.');
        }
        $this->id = $id;
    }

    public function existe(): bool
    {
        return $this->id !== null;
    }
}
