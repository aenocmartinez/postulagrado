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
            $sql = "
                SELECT DISTINCT
                    PROG.PROG_ID,
                    PROG.PROG_NOMBRE,
                    PROG.PROG_CODIGOPROGRAMA,
                    PROG.PROG_CODIGOSNIES,
                    MODA.MODA_ID,
                    METO.METO_ID,
                    NIED.NIED_ID,
                    JORN.JORN_ID,
                    UNID.UNID_ID
                FROM
                    ACADEMICO.PROGRAMA PROG,
                    ACADEMICO.UNIDADPROGRAMA UNPR,
                    ACADEMICO.UNIDAD UNID,
                    ACADEMICO.METODOLOGIA METO,
                    ACADEMICO.MODALIDAD MODA,
                    ACADEMICO.JORNADA JORN,
                    ACADEMICO.NIVELEDUCATIVO NIED
                WHERE
                    PROG.PROG_ID = UNPR.PROG_ID
                    AND UNID.UNID_ID = UNPR.UNID_ID
                    AND PROG.JORN_ID = JORN.JORN_ID
                    AND PROG.MODA_ID = MODA.MODA_ID
                    AND MODA.NIED_ID = NIED.NIED_ID
                    AND METO.METO_ID = PROG.METO_ID
                    AND UNID.UNID_REGIONAL = '1'
                    AND PROG.PROG_ESTADO = 1
                    AND NIED.NIED_ID IN (1, 2)
                    AND PROG.PROG_ID = :id
            ";
    
            $resultados = DB::connection('oracle_academico')->select($sql, ['id' => $programaID]);
    
            if (empty($resultados)) {
                Log::warning("No se encontró el programa con ID {$programaID}");
                return $programa;
            }
    
            $registro = $resultados[0];
    
            // Guardamos IDs para búsquedas adicionales
            $this->programa_id = $registro->PROG_ID;
            $this->modalidad_id = $registro->MODA_ID;
            $this->metodologia_id = $registro->METO_ID;
            $this->nivel_id = $registro->NIED_ID;
            $this->jornada_id = $registro->JORN_ID;
    
            $programa->setId($registro->PROG_ID);
            $programa->setNombre($registro->PROG_NOMBRE);
            $programa->setCodigo($registro->PROG_CODIGOPROGRAMA);
            $programa->setSnies($registro->PROG_CODIGOSNIES);
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
        // para 30 minutos cambiar a addMinutes(30)
        return Cache::remember('programas_activos', now()->addHours(6), function () {
            $programas = [];
    
            try {
                    $sql = "
                    SELECT DISTINCT
                        METO.METO_ID                  AS METODOLOGIA_ID,
                        METO.METO_DESCRIPCION         AS METODOLOGIA,
                        NIED.NIED_ID                  AS NIVEL_ID,
                        NIED.NIED_DESCRIPCION         AS NIVEL_EDUCATIVO,
                        MODA.MODA_ID                  AS MODALIDAD_ID,
                        UPPER(MODA.MODA_DESCRIPCION)  AS MODALIDAD,
                        PROG.PROG_ID                  AS ID,
                        PROG.PROG_CODIGOSNIES         AS SNIES,
                        PROG.PROG_CODIGOPROGRAMA      AS CODIGO,
                        PROG.PROG_NOMBRE              AS NOMBRE,
                        JORN.JORN_ID                  AS JORNADA_ID,
                        JORN.JORN_DESCRIPCION         AS JORNADA,
                        UNID.UNID_ID                  AS UNIDAD_ID,
                        UNID.UNID_NOMBRE              AS UNIDAD_REGIONAL
                    FROM
                        ACADEMICO.PROGRAMA PROG
                        JOIN ACADEMICO.UNIDADPROGRAMA UNPR ON PROG.PROG_ID = UNPR.PROG_ID
                        JOIN ACADEMICO.UNIDAD UNID ON UNID.UNID_ID = UNPR.UNID_ID
                        JOIN ACADEMICO.JORNADA JORN ON PROG.JORN_ID = JORN.JORN_ID
                        JOIN ACADEMICO.MODALIDAD MODA ON PROG.MODA_ID = MODA.MODA_ID
                        JOIN ACADEMICO.NIVELEDUCATIVO NIED ON MODA.NIED_ID = NIED.NIED_ID
                        JOIN ACADEMICO.METODOLOGIA METO ON METO.METO_ID = PROG.METO_ID
                    WHERE
                        UNID.UNID_REGIONAL = '1'
                        AND NIED.NIED_ID IN (1, 2)
                        AND PROG.PROG_ESTADO = 1
                    ORDER BY
                        METO.METO_ID,
                        NIED.NIED_ID,
                        MODA.MODA_ID,
                        PROG.PROG_NOMBRE,
                        UNID.UNID_NOMBRE
                ";

                $registros = DB::connection('oracle_academico')->select($sql);
    
                foreach ($registros as $registro) {
                    $programa = new Programa(
                        FabricaDeRepositorios::getInstance()->getProgramaRepository()
                    );
    
                    $programa->setId($registro->ID);
                    $programa->setNombre($registro->NOMBRE);
                    $programa->setCodigo($registro->CODIGO);
                    $programa->setSnies($registro->SNIES);
    
                    // Objetos relacionados con inyección
                    $modalidad = new Modalidad(FabricaDeRepositorios::getInstance()->getModalidadRepository());
                    $modalidad->setId((int) $registro->MODALIDAD_ID);
                    $modalidad->setNombre((string) $registro->MODALIDAD);
    
                    $metodologia = new Metodologia(FabricaDeRepositorios::getInstance()->getMetodologiaRepository());
                    $metodologia->setId((int) $registro->METODOLOGIA_ID);
                    $metodologia->setNombre((string) $registro->METODOLOGIA);
    
                    $nivel = new NivelEducativo(FabricaDeRepositorios::getInstance()->getNivelEducativoRepository());
                    $nivel->setId((int) $registro->NIVEL_ID);
                    $nivel->setNombre((string) $registro->NIVEL_EDUCATIVO);
    
                    $jornada = new Jornada(FabricaDeRepositorios::getInstance()->getJornadaRepository());
                    $jornada->setId((int) $registro->JORNADA_ID);
                    $jornada->setNombre((string) $registro->JORNADA);
    
                    $unidad = new UnidadRegional(FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository());
                    $unidad->setId((int) $registro->UNIDAD_ID);
                    $unidad->setNombre((string) $registro->UNIDAD_REGIONAL);
    
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
