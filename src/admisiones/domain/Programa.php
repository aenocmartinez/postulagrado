<?php

namespace Src\admisiones\domain;

use Src\admisiones\repositories\ProgramaRepository;
use Src\shared\formato\FormatoString;

class Programa 
{
    private string $nombre;
    private int $snies;
    private int $codigo;
    private Metodologia $metodologia;
    private NivelEducativo $nivelEducativo;
    private Modalidad $modalidad;
    private Jornada $jornada;
    private UnidadRegional $unidadRegional;

    public function __construct(
        private ProgramaRepository $programaRepo,
        private int $id = 0
        ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNombre(): string
    {        
        return FormatoString::capital($this->nombre);
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getSnies(): int
    {
        return $this->snies;
    }

    public function setSnies(int $snies): void
    {
        $this->snies = $snies;
    }

    public function getCodigo(): int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getMetodologia(): Metodologia
    {
        return $this->metodologia;
    }

    public function setMetodologia(Metodologia $metodologia): void
    {
        $this->metodologia = $metodologia;
    }

    public function getNivelEducativo(): NivelEducativo
    {
        return $this->nivelEducativo;
    }

    public function setNivelEducativo(NivelEducativo $nivelEducativo): void
    {
        $this->nivelEducativo = $nivelEducativo;
    }

    public function getModalidad(): Modalidad
    {
        return $this->modalidad;
    }

    public function setModalidad(Modalidad $modalidad): void
    {
        $this->modalidad = $modalidad;
    }

    public function getJornada(): Jornada
    {
        return $this->jornada;
    }

    public function setJornada(Jornada $jornada): void
    {
        $this->jornada = $jornada;
    }

    public function getUnidadRegional(): UnidadRegional
    {
        return $this->unidadRegional;
    }

    public function setUnidadRegional(UnidadRegional $unidadRegional): void
    {
        $this->unidadRegional = $unidadRegional;
    }

    public function getEstudiantesCandidatosAGradoPorPeriodo(int $periodoAnio, int $periodoNumero): array
    {
        return $this->programaRepo->buscarEstudiantesCandidatosAGrado($this->codigo, $periodoAnio, $periodoNumero);
    }

    public function tieneCandidatosAsocidos(int $procesoID=0): bool {
        return $this->programaRepo->tieneCandidatosAsociados($procesoID, $this->id);
    }

    public function listarEstudiantesCandidatos(int $procesoID = 0): array
    {        
        $candidatos = $this->programaRepo->listarEstudiantesCandidatos($this->id, $procesoID);
        if (empty($candidatos)) {
            return [];
        }

        $codigos = array_map(fn($c) => $c->estu_codigo, $candidatos);
        
        $detalles = $this->programaRepo->obtenerEstudiantePorCodigo($codigos); 

        $detallesIndexados = [];
        foreach ($detalles as $detalle) {
            $codigo = $detalle->estp_codigomatricula;
            $detallesIndexados[$codigo] = $detalle;
        }

        return array_map(function ($candidato) use ($detallesIndexados) {
            $codigo = $candidato->estu_codigo;

            return [
                'ppes_id'     => $candidato->ppes_id,
                'estu_codigo' => $codigo,
                'detalle'     => $detallesIndexados[$codigo] ?? null,
            ];
        }, $candidatos);
        
    }

    public function existe(): bool {
        return $this->id > 0;
    }
}
