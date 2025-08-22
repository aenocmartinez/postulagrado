<?php

namespace Src\domain\repositories;

use Src\domain\Jornada;
use Src\domain\Metodologia;
use Src\domain\Modalidad;
use Src\domain\NivelEducativo;
use Src\domain\programa\Programa;
use Src\domain\UnidadRegional;

interface ProgramaRepository {
    public function metodologia(): Metodologia;
    public function nivelEducativo(): NivelEducativo;
    public function modalidad(): Modalidad;
    public function jornada(): Jornada;
    public function unidadRegional(): UnidadRegional;
    public function buscarProgramasPorNivelEducativo(int $nivelEducativoID): array;
    public function buscarPorID(int $programaID): Programa;
    public function listarProgramas(): array;
    public function buscarEstudiantesCandidatosAGrado(int $codigoPrograma, int $anio, int $periodo): array;
    public function tieneCandidatosAsociados(int $procesoID, int $programaID): bool;
    public function listarEstudiantesCandidatos(int $programaID, int $procesoID): array;
    public function obtenerEstudiantePorCodigo(string|array $codigosEstudiante): array;
    public function quitarEstudiante(int $estudianteProcesoProgramaID);
}