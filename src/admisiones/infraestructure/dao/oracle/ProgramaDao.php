<?php

namespace Src\admisiones\infraestructure\dao\oracle;

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

use Illuminate\Support\Facades\Cache;

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
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.PROGRAMA AS PROG')
                ->join('ACADEMICO.UNIDADPROGRAMA AS UNPR', 'PROG.PROG_ID', '=', 'UNPR.PROG_ID')
                ->join('ACADEMICO.UNIDAD AS UNID', 'UNID.UNID_ID', '=', 'UNPR.UNID_ID')
                ->join('ACADEMICO.METODOLOGIA AS METO', 'METO.METO_ID', '=', 'PROG.METO_ID')
                ->join('ACADEMICO.MODALIDAD AS MODA', 'PROG.MODA_ID', '=', 'MODA.MODA_ID')
                ->join('ACADEMICO.JORNADA AS JORN', 'PROG.JORN_ID', '=', 'JORN.JORN_ID')
                ->join('ACADEMICO.NIVELEDUCATIVO AS NIED', 'MODA.NIED_ID', '=', 'NIED.NIED_ID')
                ->select(
                    'PROG.PROG_ID',
                    'PROG.PROG_NOMBRE',
                    'PROG.PROG_CODIGOPROGRAMA',
                    'PROG.PROG_CODIGOSNIES',
                    'MODA.MODA_ID',
                    'METO.METO_ID',
                    'NIED.NIED_ID',
                    'JORN.JORN_ID',
                    'UNID.UNID_ID'
                )
                ->where('PROG.PROG_ID', $programaID)
                ->where('PROG.PROG_ESTADO', 1)
                ->where('UNID.UNID_REGIONAL', '1')
                ->whereIn('NIED.NIED_ID', [1, 2])
                ->first();
    
            if (!$registro) {
                Log::warning("No se encontró el programa con ID {$programaID}");
                return $programa;
            }
    
            // Guardamos IDs para búsquedas adicionales
            $this->programa_id = $registro->prog_id;
            $this->modalidad_id = $registro->moda_id;
            $this->metodologia_id = $registro->meto_id;
            $this->nivel_id = $registro->nied_id;
            $this->jornada_id = $registro->jorn_id;
    
            $programa->setId($registro->prog_id);
            $programa->setNombre($registro->prog_nombre);
            $programa->setCodigo($registro->prog_codigoprograma);
            $programa->setSnies($registro->prog_codigosnies);
            $programa->setModalidad($this->modalidad());
            $programa->setMetodologia($this->metodologia());
            $programa->setNivelEducativo($this->nivelEducativo());
            $programa->setJornada($this->jornada());
            $programa->setUnidadRegional($this->unidadRegional());
    
        } catch (\Exception $e) {
            Log::error("ProgramaDao / buscarPorID: " . $e->getMessage());
        }
    
        return $programa;
    }      

    public function listarProgramas(): array
    {
        return Cache::remember('programas_activos', now()->addHours(6), function () {
            $programas = [];
    
            try {
                $registros = DB::connection('oracle_academico')
                    ->table('ACADEMICO.PROGRAMA AS PROG')
                    ->join('ACADEMICO.UNIDADPROGRAMA AS UNPR', 'PROG.PROG_ID', '=', 'UNPR.PROG_ID')
                    ->join('ACADEMICO.UNIDAD AS UNID', 'UNID.UNID_ID', '=', 'UNPR.UNID_ID')
                    ->join('ACADEMICO.JORNADA AS JORN', 'PROG.JORN_ID', '=', 'JORN.JORN_ID')
                    ->join('ACADEMICO.MODALIDAD AS MODA', 'PROG.MODA_ID', '=', 'MODA.MODA_ID')
                    ->join('ACADEMICO.NIVELEDUCATIVO AS NIED', 'MODA.NIED_ID', '=', 'NIED.NIED_ID')
                    ->join('ACADEMICO.METODOLOGIA AS METO', 'METO.METO_ID', '=', 'PROG.METO_ID')
                    ->select(
                        'METO.METO_ID AS metodologia_id',
                        'METO.METO_DESCRIPCION AS metodologia',
                        'NIED.NIED_ID AS nivel_id',
                        'NIED.NIED_DESCRIPCION AS nivel_educativo',
                        'MODA.MODA_ID AS modalidad_id',
                        DB::raw('UPPER(MODA.MODA_DESCRIPCION) AS modalidad'),
                        'PROG.PROG_ID AS id',
                        'PROG.PROG_CODIGOSNIES AS snies',
                        'PROG.PROG_CODIGOPROGRAMA AS codigo',
                        'PROG.PROG_NOMBRE AS nombre',
                        'JORN.JORN_ID AS jornada_id',
                        'JORN.JORN_DESCRIPCION AS jornada',
                        'UNID.UNID_ID AS unidad_id',
                        'UNID.UNID_NOMBRE AS unidad_regional'
                    )
                    ->where('UNID.UNID_REGIONAL', '1')
                    ->whereIn('NIED.NIED_ID', [1, 2])
                    ->where('PROG.PROG_ESTADO', 1)
                    ->orderBy('METO.METO_ID')
                    ->orderBy('NIED.NIED_ID')
                    ->orderBy('MODA.MODA_ID')
                    ->orderBy('PROG.PROG_NOMBRE')
                    ->orderBy('UNID.UNID_NOMBRE')
                    ->get();
    
                foreach ($registros as $registro) {
                    $programa = new Programa(
                        FabricaDeRepositorios::getInstance()->getProgramaRepository()
                    );
    
                    $programa->setId($registro->id);
                    $programa->setNombre($registro->nombre);
                    $programa->setCodigo($registro->codigo);
                    $programa->setSnies($registro->snies);
    
                    $modalidad = new Modalidad(FabricaDeRepositorios::getInstance()->getModalidadRepository());
                    $modalidad->setId((int) $registro->modalidad_id);
                    $modalidad->setNombre((string) $registro->modalidad);
    
                    $metodologia = new Metodologia(FabricaDeRepositorios::getInstance()->getMetodologiaRepository());
                    $metodologia->setId((int) $registro->metodologia_id);
                    $metodologia->setNombre((string) $registro->metodologia);
    
                    $nivel = new NivelEducativo(FabricaDeRepositorios::getInstance()->getNivelEducativoRepository());
                    $nivel->setId((int) $registro->nivel_id);
                    $nivel->setNombre((string) $registro->nivel_educativo);
    
                    $jornada = new Jornada(FabricaDeRepositorios::getInstance()->getJornadaRepository());
                    $jornada->setId((int) $registro->jornada_id);
                    $jornada->setNombre((string) $registro->jornada);
    
                    $unidad = new UnidadRegional(FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository());
                    $unidad->setId((int) $registro->unidad_id);
                    $unidad->setNombre((string) $registro->unidad_regional);
    
                    $programa->setModalidad($modalidad);
                    $programa->setMetodologia($metodologia);
                    $programa->setNivelEducativo($nivel);
                    $programa->setJornada($jornada);
                    $programa->setUnidadRegional($unidad);
    
                    $programas[] = $programa;
                }
            } catch (Exception $e) {
                Log::error("Error al listar programas: " . $e->getMessage());
            }
    
            return $programas;
        });
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
                ->table('ACADEMICO.PROGRAMA AS PROG')
                ->join('ACADEMICO.MODALIDAD AS MODA', 'MODA.MODA_ID', '=', 'PROG.MODA_ID')
                ->join('ACADEMICO.NIVELEDUCATIVO AS NIED', 'NIED.NIED_ID', '=', 'MODA.NIED_ID')
                ->select(
                    'PROG.PROG_ID AS id',
                    'PROG.PROG_NOMBRE AS nombre',
                    'PROG.PROG_CODIGOPROGRAMA AS codigo',
                    'PROG.PROG_CODIGOSNIES AS snies',
                    'NIED.NIED_ID AS nivel_id',
                    'NIED.NIED_DESCRIPCION AS nivel_nombre'
                )
                ->where('NIED.NIED_ID', $nivelEducativoID)
                ->where('PROG.PROG_ESTADO', 1)
                ->orderBy('PROG.PROG_NOMBRE')
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
