<?php

namespace Src\admisiones\dao\oracle;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Jornada;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\Programa;
use Src\admisiones\domain\UnidadRegional;
use Src\admisiones\repositories\ProgramaRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaDao implements ProgramaRepository
{
    private $metodologia_id;
    private $nivel_id;
    private $modalidad_id;
    private $jornada_id;
    private $programa_id;

    public function metodologia(): Metodologia
    {
        return FabricaDeRepositorios::getInstance()
            ->getMetodologiaRepository()
            ->buscarPorID($this->metodologia_id);
    }

    public function nivelEducativo(): NivelEducativo
    {
        return FabricaDeRepositorios::getInstance()
            ->getNivelEducativoRepository()
            ->buscarPorID($this->nivel_id);
    }

    public function modalidad(): Modalidad
    {
        return FabricaDeRepositorios::getInstance()
            ->getModalidadRepository()
            ->buscarPorID($this->modalidad_id);
    }

    public function jornada(): Jornada
    {
        return FabricaDeRepositorios::getInstance()
            ->getJornadaRepository()
            ->buscarPorID($this->jornada_id);
    }

    public function unidadRegional(): UnidadRegional
    {
        return FabricaDeRepositorios::getInstance()
            ->getUnidadRegionalRepository()
            ->buscarPorID($this->programa_id);
    }

    public function buscarPorID(int $programaID): Programa
    {
        $programa = new Programa(
            FabricaDeRepositorios::getInstance()->getProgramaRepository()
        );

        try {
            $registro = DB::connection('oracle_academico')->table('ACADEMICO.PROGRAMA AS PROG')
                ->select('PROG.PROG_ID AS id', 'PROG.PROG_NOMBRE AS nombre', 'PROG.PROG_CODIGO AS codigo', 'PROG.PROG_SNIES AS snies',
                        'PROG.MOD_ID AS modalidad_id', 'PROG.METOD_ID AS metodologia_id', 'PROG.NIVEL_ID AS nivel_id', 'PROG.JORN_ID AS jornada_id')
                ->where('PROG.PROG_ID', $programaID)
                ->first();

            if (!$registro) {
                Log::warning("No se encontró el programa con ID {$programaID}");
                return $programa;
            }

            $this->programa_id = $registro->id;
            $this->modalidad_id = $registro->modalidad_id;
            $this->metodologia_id = $registro->metodologia_id;
            $this->nivel_id = $registro->nivel_id;
            $this->jornada_id = $registro->jornada_id;

            $programa->setId($registro->id);
            $programa->setNombre($registro->nombre);
            $programa->setCodigo($registro->codigo);
            $programa->setSnies($registro->snies);
            $programa->setModalidad($this->modalidad());
            $programa->setMetodologia($this->metodologia());
            $programa->setNivelEducativo($this->nivelEducativo());
            $programa->setJornada($this->jornada());
            $programa->setUnidadRegional($this->unidadRegional());

        } catch (Exception $e) {
            Log::error("Error al buscar el programa por ID {$programaID}: " . $e->getMessage());
        }

        return $programa;
    }

    public function listarProgramas(): array
    {
        $programas = [];

        try {
            $registros = DB::connection('oracle_academico')->table('ACADEMICO.PROGRAMA AS PROG')
                ->select('PROG.PROG_ID AS id', 'PROG.PROG_NOMBRE AS nombre', 'PROG.PROG_CODIGO AS codigo', 'PROG.PROG_SNIES AS snies',
                        'PROG.MOD_ID AS modalidad_id', 'PROG.METOD_ID AS metodologia_id', 'PROG.NIVEL_ID AS nivel_id', 'PROG.JORN_ID AS jornada_id')
                ->get();

            foreach ($registros as $registro) {
                $this->programa_id = $registro->id;
                $this->modalidad_id = $registro->modalidad_id;
                $this->metodologia_id = $registro->metodologia_id;
                $this->nivel_id = $registro->nivel_id;
                $this->jornada_id = $registro->jornada_id;

                $programa = new Programa(
                    FabricaDeRepositorios::getInstance()->getProgramaRepository()
                );

                $programa->setId($registro->id);
                $programa->setNombre($registro->nombre);
                $programa->setCodigo($registro->codigo);
                $programa->setSnies($registro->snies);
                $programa->setModalidad($this->modalidad());
                $programa->setMetodologia($this->metodologia());
                $programa->setNivelEducativo($this->nivelEducativo());
                $programa->setJornada($this->jornada());
                $programa->setUnidadRegional($this->unidadRegional());

                $programas[] = $programa;
            }

        } catch (Exception $e) {
            Log::error("Error al listar programas: " . $e->getMessage());
        }

        return $programas;
    }

    public function buscarProgramasPorNivelEducativo(string $nombreNivelEducativo): array
    {
        $programas = [];
    
        $nivelEducativoID = 1;
        if (strtolower(trim($nombreNivelEducativo)) === "postgrado") {
            $nivelEducativoID = 2;
        }
    
        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.PROGRAMA')
                ->join('ACADEMICO.MODALIDAD', 'MODALIDAD.MODA_ID', '=', 'PROGRAMA.MODA_ID')
                ->join('ACADEMICO.NIVELEDUCATIVO', 'NIVELEDUCATIVO.NIED_ID', '=', 'MODALIDAD.NIED_ID')
                ->select(
                    'PROGRAMA.PROG_ID AS id',
                    'PROGRAMA.PROG_NOMBRE AS nombre',
                    'PROGRAMA.PROG_CODIGOPROGRAMA AS codigo',
                    'PROGRAMA.PROG_CODIGOSNIES AS snies',
                    'NIVELEDUCATIVO.NIED_ID AS nivel_id',
                    'NIVELEDUCATIVO.NIED_DESCRIPCION AS nivel_nombre'
                )
                ->where('NIVELEDUCATIVO.NIED_ID', $nivelEducativoID)
                ->where('PROGRAMA.PROG_ESTADO', 1)
                ->orderBy('PROGRAMA.PROG_NOMBRE')
                ->get();
    
            foreach ($registros as $registro) {
                $programa = new Programa(
                    FabricaDeRepositorios::getInstance()->getProgramaRepository()
                );
    
                $programa->setId($registro->id);
                $programa->setNombre($registro->nombre);
                $programa->setCodigo($registro->codigo);
                $programa->setSnies($registro->snies);
    
                $nivel = new NivelEducativo(
                    FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
                );
                $nivel->setId($registro->nivel_id);
                $nivel->setNombre($registro->nivel_nombre);
                $programa->setNivelEducativo($nivel);
    
                $programas[] = $programa;
            }
    
        } catch (Exception $e) {
            Log::error("Error al buscar programas por nivel educativo '{$nombreNivelEducativo}': " . $e->getMessage());
        }
    
        return $programas;
    }    
}
