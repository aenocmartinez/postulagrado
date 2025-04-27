<?php

namespace Src\admisiones\repositories;

use Src\admisiones\domain\Jornada;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\Programa;
use Src\admisiones\domain\UnidadRegional;

interface ProgramaRepository {
    public function metodologia(): Metodologia;
    public function nivelEducativo(): NivelEducativo;
    public function modalidad(): Modalidad;
    public function jornada(): Jornada;
    public function unidadRegional(): UnidadRegional;
    public function buscarProgramasPorNivelEducativo(int $nivelEducativoID): array;
    public function buscarPorID(int $programaID): Programa;
    public function listarProgramas(): array;
}