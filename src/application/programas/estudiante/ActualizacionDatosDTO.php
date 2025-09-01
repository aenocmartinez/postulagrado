<?php

// Src/application/programas/estudiante/ActualizacionDatosDTO.php

namespace Src\application\programas\estudiante;

use Illuminate\Http\UploadedFile;

class ActualizacionDatosDTO
{
    public int $enlace_id;
    public int $proceso_id;
    public ?int $programa_id = null;
    public string $codigo;

    public ?UploadedFile $doc_identificacion = null;
    public ?UploadedFile $cert_saber = null;

    public ?string $codigo_saber = null;
    public ?string $grupo_investigacion = null;
    public ?string $nombre_grupo = null;
    public ?string $telefono = null;
    public ?string $correo_personal = null;
    public ?string $departamento = null;
    public ?string $ciudad = null;
    public ?string $direccion = null;

    public ?string $hijo_funcionario = null;
    public ?string $hijo_docente = null;
    public ?string $es_funcionario = null;
    public ?string $es_docente = null;

    public bool $es_postgrado = false;
    public ?string $titulo_pregrado = null;
    public ?string $universidad_pregrado = null;
    public ?string $fecha_grado_pregrado = null;

    public static function desdeArray(array $datos): self
    {
        $dto = new self();
        $dto->enlace_id    = (int)($datos['enlace_id'] ?? 0);
        $dto->proceso_id   = (int)($datos['proceso_id'] ?? 0);
        $dto->programa_id  = isset($datos['programa_id']) ? (int)$datos['programa_id'] : null; // <---
        $dto->codigo       = (string)($datos['codigo'] ?? '');

        $dto->doc_identificacion = $datos['doc_identificacion'] ?? null;
        $dto->cert_saber         = $datos['cert_saber'] ?? null;

        $dto->codigo_saber       = $datos['codigo_saber'] ?? null;
        $dto->grupo_investigacion = $datos['grupo_investigacion'] ?? null;
        $dto->nombre_grupo       = $datos['nombre_grupo'] ?? null;
        $dto->telefono           = $datos['telefono'] ?? null;
        $dto->correo_personal    = $datos['correo_personal'] ?? null;
        $dto->departamento       = $datos['departamento'] ?? null;
        $dto->ciudad             = $datos['ciudad'] ?? null;
        $dto->direccion          = $datos['direccion'] ?? null;

        $dto->hijo_funcionario   = $datos['hijo_funcionario'] ?? null;
        $dto->hijo_docente       = $datos['hijo_docente'] ?? null;
        $dto->es_funcionario     = $datos['es_funcionario'] ?? null;
        $dto->es_docente         = $datos['es_docente'] ?? null;

        $dto->es_postgrado       = (bool)($datos['es_postgrado'] ?? false);
        $dto->titulo_pregrado    = $datos['titulo_pregrado'] ?? null;
        $dto->universidad_pregrado = $datos['universidad_pregrado'] ?? null;
        $dto->fecha_grado_pregrado = $datos['fecha_grado_pregrado'] ?? null;

        return $dto;
    }

    public function aArray(): array
    {
        return get_object_vars($this);
    }
}
