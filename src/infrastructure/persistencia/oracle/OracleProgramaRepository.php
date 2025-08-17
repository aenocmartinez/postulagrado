<?php

namespace Src\infrastructure\persistencia\oracle;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\Jornada;
use Src\domain\Metodologia;
use Src\domain\Modalidad;
use Src\domain\NivelEducativo;
use Src\domain\Programa;
use Src\domain\UnidadRegional;
use Src\shared\di\FabricaDeRepositoriosOracle;

use Illuminate\Support\Facades\Cache;
use Src\domain\Estudiante;
use Src\domain\repositories\ProgramaRepository;

class OracleProgramaRepository implements ProgramaRepository
{
    private $metodologia_id;
    private $nivel_id;
    private $modalidad_id;
    private $jornada_id;
    private $programa_id;

    public function metodologia(): Metodologia
    {
        return FabricaDeRepositoriosOracle::getInstance()
            ->getMetodologiaRepository()
            ->buscarPorID($this->metodologia_id);
    }

    public function nivelEducativo(): NivelEducativo
    {
        return FabricaDeRepositoriosOracle::getInstance()
            ->getNivelEducativoRepository()
            ->buscarPorID($this->nivel_id);
    }

    public function modalidad(): Modalidad
    {
        return FabricaDeRepositoriosOracle::getInstance()
            ->getModalidadRepository()
            ->buscarPorID($this->modalidad_id);
    }

    public function jornada(): Jornada
    {
        return FabricaDeRepositoriosOracle::getInstance()
            ->getJornadaRepository()
            ->buscarPorID($this->jornada_id);
    }

    public function unidadRegional(): UnidadRegional
    {
        return FabricaDeRepositoriosOracle::getInstance()
            ->getUnidadRegionalRepository()
            ->buscarPorID($this->programa_id);
    }

    public function buscarPorID(int $programaID): Programa
    {
        return Cache::remember('programa_' . $programaID, now()->addHours(4), function () use ($programaID) {
            $programa = new Programa();
    
            try {
                $registro = DB::connection('oracle_academico')
                    ->table('ACADEMICO.PROGRAMA PROG')
                    ->join('ACADEMICO.UNIDADPROGRAMA UNPR', 'PROG.PROG_ID', '=', 'UNPR.PROG_ID')
                    ->join('ACADEMICO.UNIDAD UNID', 'UNID.UNID_ID', '=', 'UNPR.UNID_ID')
                    ->join('ACADEMICO.METODOLOGIA METO', 'METO.METO_ID', '=', 'PROG.METO_ID')
                    ->join('ACADEMICO.MODALIDAD MODA', 'PROG.MODA_ID', '=', 'MODA.MODA_ID')
                    ->join('ACADEMICO.JORNADA JORN', 'PROG.JORN_ID', '=', 'JORN.JORN_ID')
                    ->join('ACADEMICO.NIVELEDUCATIVO NIED', 'MODA.NIED_ID', '=', 'NIED.NIED_ID')
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
        });
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
                    $programa = new Programa();
    
                    $programa->setId($registro->id);
                    $programa->setNombre($registro->nombre);
                    $programa->setCodigo($registro->codigo);
                    $programa->setSnies($registro->snies);
    
                    $modalidad = new Modalidad();
                    $modalidad->setId((int) $registro->modalidad_id);
                    $modalidad->setNombre((string) $registro->modalidad);
    
                    $metodologia = new Metodologia();
                    $metodologia->setId((int) $registro->metodologia_id);
                    $metodologia->setNombre((string) $registro->metodologia);
    
                    $nivel = new NivelEducativo();
                    $nivel->setId((int) $registro->nivel_id);
                    $nivel->setNombre((string) $registro->nivel_educativo);
    
                    $jornada = new Jornada();
                    $jornada->setId((int) $registro->jornada_id);
                    $jornada->setNombre((string) $registro->jornada);
    
                    $unidad = new UnidadRegional();
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
        
    public function buscarProgramasPorNivelEducativo(int $nivelEducativoID): array
    {
        return Cache::remember('programas_nivel_' . $nivelEducativoID, now()->addHours(4), function () use ($nivelEducativoID) {
            $programas = [];
    
            try {
                $registros = DB::connection('oracle_academico')
                    ->table('ACADEMICO.PROGRAMA PROG')
                    ->join('ACADEMICO.MODALIDAD MODA', 'MODA.MODA_ID', '=', 'PROG.MODA_ID')
                    ->join('ACADEMICO.NIVELEDUCATIVO NIED', 'NIED.NIED_ID', '=', 'MODA.NIED_ID')
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
                    $programa = new Programa();
    
                    $programa->setId($registro->id);
                    $programa->setNombre($registro->nombre);
                    $programa->setCodigo($registro->codigo);
                    $programa->setSnies($registro->snies);
    
                    $nivel = new NivelEducativo();
                    $nivel->setId($registro->nivel_id);
                    $nivel->setNombre($registro->nivel_nombre);
                    $programa->setNivelEducativo($nivel);
    
                    $programas[] = $programa;
                }
    
            } catch (Exception $e) {
                Log::error("Error al buscar programas por nivel educativo '{$nivelEducativoID}': " . $e->getMessage());
            }
    
            return $programas;
        });
    }    

    public function buscarEstudiantesCandidatosAGrado(int $codigoPrograma, int $anio, int $periodo): array
    {
        try {


            $sql = <<<SQL
            SELECT DISTINCT
                PROG.PROG_NOMBRE AS programa_academico,
                CLAVE2.PENSUMNOMBRE AS pensum_est,
                ESTP.ESTP_CODIGOMATRICULA AS codigo_est,
                PEGE.PEGE_DOCUMENTOIDENTIDAD AS documento,
                PENG.PENG_PRIMERAPELLIDO || ' ' || PENG.PENG_SEGUNDOAPELLIDO || ' ' || PENG.PENG_PRIMERNOMBRE || ' ' || PENG.PENG_SEGUNDONOMBRE AS nombres,
                ESTP.ESTP_PERIODOACADEMICO AS ubicacion_semestral,
                CATE.CATE_DESCRIPCION AS categoria,
                SITE.SITE_DESCRIPCION AS situacion,
                CLAVE2.TOTALCREDITOSPENSUM AS total_creditos_pensum,
                (CLAVE2.TOTALCREDITOSPENSUM - ESTP.ESTP_CREDITOSAPROBADOS) AS creditos_pendientes,
                CLAVE2.PONDERACIONBASICA AS area_basica_pensum,
                CLAVE2.CLAVE2PONDERA AS creditos_aprob_area_basica,
                (CLAVE2.PONDERACIONBASICA - CLAVE2.CLAVE2PONDERA) AS creditos_pend_area_basica,
                CLAVE5.AREAPROFUNDIZACIONPROGRAMA AS total_cred_profund_pens,
                CLAVE1.CLAVE1PONDERA AS creditos_aprob_profundizacion,
                (CLAVE5.AREAPROFUNDIZACIONPROGRAMA - CLAVE1.CLAVE1PONDERA) AS creditos_pend_profundizacion,
                CLAVE4.AREACOMPLEMENTARIAPROGRAMA AS area_comple_pens,
                CLAVE3.CLAVE3PONDERA AS creditos_aprob_electiva_comple,
                (CLAVE4.AREACOMPLEMENTARIAPROGRAMA - CLAVE3.CLAVE3PONDERA) AS creditos_pend_electiva_comple
            FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
            JOIN ACADEMICO.UNIDADPROGRAMA UNPR ON ESTP.UNPR_ID = UNPR.UNPR_ID
            JOIN ACADEMICO.PROGRAMA PROG ON UNPR.PROG_ID = PROG.PROG_ID
            JOIN GENERAL.PERSONAGENERAL PEGE ON ESTP.PEGE_ID = PEGE.PEGE_ID
            JOIN GENERAL.PERSONANATURALGENERAL PENG ON PEGE.PEGE_ID = PENG.PEGE_ID
            JOIN ACADEMICO.CATEGORIA CATE ON ESTP.CATE_ID = CATE.CATE_ID
            JOIN ACADEMICO.SITUACIONESTUDIANTE SITE ON ESTP.SITE_ID = SITE.SITE_ID
            JOIN ACADEMICO.PERIODOUNIVERSIDAD PEUN ON ESTP.PEUN_ID = PEUN.PEUN_ID

            LEFT JOIN (
                SELECT ESTP.ESTP_ID, SUM(REAC.REAC_PONDERACIONACADEMICA) AS CLAVE1PONDERA
                FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                JOIN ACADEMICO.REGISTROACADEMICO REAC ON ESTP.ESTP_ID = REAC.ESTP_ID
                WHERE REAC.REAC_APROBADO = 1
                AND REAC.REAC_CUENTAPROMEDIO = 1
                AND REGEXP_LIKE(REAC.MATE_CODIGOMATERIA, '15|16|17|18|19|20$')
                GROUP BY ESTP.ESTP_ID
            ) CLAVE1 ON ESTP.ESTP_ID = CLAVE1.ESTP_ID

            LEFT JOIN (
                SELECT ESTP.ESTP_ID, PENS.PENS_TOTALCREDITOS AS TOTALCREDITOSPENSUM,
                    SUM(MATE.MATE_PONDERACIONACADEMICA) AS CLAVE2PONDERA,
                    PENS.PENS_PONMINMATNOR AS PONDERACIONBASICA,
                    PENS.PENS_DESCRIPCION AS PENSUMNOMBRE
                FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                JOIN ACADEMICO.PENSUM PENS ON ESTP.PENS_ID = PENS.PENS_ID
                JOIN ACADEMICO.PENSUMMATERIA PEMA ON PENS.PENS_ID = PEMA.PENS_ID
                JOIN ACADEMICO.MATERIA MATE ON MATE.MATE_CODIGOMATERIA = PEMA.MATE_CODIGOMATERIA
                JOIN ACADEMICO.REGISTROACADEMICO REAC ON REAC.ESTP_ID = ESTP.ESTP_ID AND REAC.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
                WHERE PEMA.CICU_ID = 4
                AND REAC.REAC_APROBADO = 1
                AND PENS.TIPA_ID = 2
                AND (ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS - 1 OR ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS)
                GROUP BY ESTP.ESTP_ID, PENS.PENS_TOTALCREDITOS, PENS.PENS_PONMINMATNOR, PENS.PENS_DESCRIPCION
            ) CLAVE2 ON ESTP.ESTP_ID = CLAVE2.ESTP_ID

            LEFT JOIN (
                SELECT ESTP.ESTP_ID, SUM(REAC.REAC_PONDERACIONACADEMICA) AS CLAVE3PONDERA
                FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                JOIN ACADEMICO.REGISTROACADEMICO REAC ON ESTP.ESTP_ID = REAC.ESTP_ID
                WHERE REAC.REAC_APROBADO = 1
                AND REAC.REAC_CUENTAPROMEDIO = 1
                AND REAC.MATE_CODIGOMATERIA LIKE '%14'
                GROUP BY ESTP.ESTP_ID
            ) CLAVE3 ON ESTP.ESTP_ID = CLAVE3.ESTP_ID

            LEFT JOIN (
                SELECT ESTP.ESTP_ID, SUM(MATE.MATE_PONDERACIONACADEMICA) AS AREACOMPLEMENTARIAPROGRAMA
                FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                JOIN ACADEMICO.PENSUM PENS ON ESTP.PENS_ID = PENS.PENS_ID
                JOIN ACADEMICO.PENSUMMATERIA PEMA ON PENS.PENS_ID = PEMA.PENS_ID AND PEMA.CAFO_ID = 22
                JOIN ACADEMICO.MATERIA MATE ON MATE.MATE_CODIGOMATERIA = PEMA.MATE_CODIGOMATERIA
                GROUP BY ESTP.ESTP_ID
            ) CLAVE4 ON ESTP.ESTP_ID = CLAVE4.ESTP_ID

            LEFT JOIN (
                SELECT ESTP.ESTP_ID, SUM(MATE.MATE_PONDERACIONACADEMICA) AS AREAPROFUNDIZACIONPROGRAMA
                FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                JOIN ACADEMICO.PENSUM PENS ON ESTP.PENS_ID = PENS.PENS_ID
                JOIN ACADEMICO.PENSUMMATERIA PEMA ON PENS.PENS_ID = PEMA.PENS_ID AND PEMA.CAFO_ID = 21
                JOIN ACADEMICO.MATERIA MATE ON MATE.MATE_CODIGOMATERIA = PEMA.MATE_CODIGOMATERIA
                GROUP BY ESTP.ESTP_ID
            ) CLAVE5 ON ESTP.ESTP_ID = CLAVE5.ESTP_ID

            WHERE PROG.PROG_CODIGOPROGRAMA = :codigoPrograma
            AND PEUN.PEUN_ANO = :anio
            AND PEUN.PEUN_PERIODO = :periodo
            AND PEUN.TPPA_ID = 1

            ORDER BY ESTP.ESTP_PERIODOACADEMICO DESC, nombres
            SQL;

            $resultados = DB::connection('oracle_academico')->select($sql, [
                'codigoPrograma' => $codigoPrograma,
                'anio' => $anio,
                'periodo' => $periodo,
            ]);

            $estudiantes = [];
            foreach ($resultados as $fila) {
                
                if (stripos($fila->situacion, 'excluido') !== false) {
                    continue;
                }

                $estudiante = new Estudiante();
                $estudiante->setNombre($fila->nombres);
                $estudiante->setCodigo($fila->codigo_est);
                $estudiante->setDocumento($fila->documento);
                $estudiante->setPensum($fila->pensum_est);
                $estudiante->setCategoria($fila->categoria);
                $estudiante->setSituacion($fila->situacion);
                $estudiante->setTotalCreditosPensum($fila->total_creditos_pensum);
                $estudiante->setNumeroCreditosPendientes($fila->creditos_pendientes);
                $estudiante->setNumeroCreditosAprobadosAreaBasica($fila->creditos_aprob_area_basica);
                $estudiante->setNumeroCreditosPendientesAreaBasica($fila->creditos_pend_area_basica);
                $estudiante->setNumeroCreditosAreaProfundizacion($fila->total_cred_profund_pens);
                $estudiante->setNumeroCreditosAprobadosAreaProfundizacion($fila->creditos_aprob_profundizacion);
                $estudiante->setNumeroCreditosPendientesAreaProfundizacion($fila->creditos_pend_profundizacion);
                $estudiante->setNumeroCreditosElectivos($fila->area_comple_pens);
                $estudiante->setNumeroCreditosAprobadosElectivos($fila->creditos_aprob_electiva_comple);
                $estudiante->setNumeroCreditosPendientesElectivos($fila->creditos_pend_electiva_comple);
                $estudiante->setAnio($anio);
                $estudiante->setPeriodo($periodo);

                $estudiantes[] = $estudiante;
            }

            return $estudiantes;
        } catch (\Exception $e) {
            Log::error("Error al listar estudiantes candidatos: " . $e->getMessage());
            return [];
        }
    }

    public function tieneCandidatosAsociados(int $procesoID, int $programaID): bool
    {
        $resultado = DB::connection('oracle_academpostulgrado')->selectOne(
            "SELECT DISTINCT 'T' AS tiene 
            FROM ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES ppe
            INNER JOIN ACADEMPOSTULGRADO.PROCESO_PROGRAMA pp ON pp.PROGR_ID = ppe.PROGR_ID
            WHERE pp.PROC_ID = :proceso_id AND pp.PROG_ID = :programa_id",
            [
                'proceso_id' => $procesoID,
                'programa_id' => $programaID
            ]
        );

        return $resultado !== null;
    }

    public function listarEstudiantesCandidatos(int $programaID, int $procesoID): array
    {
        $sql = "
            SELECT 
                ppes.PPES_ID,
                ppes.ESTU_CODIGO
            FROM ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES ppes
            INNER JOIN ACADEMPOSTULGRADO.PROCESO_PROGRAMA pp
                ON pp.PROGR_ID = ppes.PROGR_ID
            WHERE pp.PROC_ID = :proceso_id
            AND pp.PROG_ID = :programa_id
        ";

        return DB::connection('oracle_academpostulgrado')->select($sql, [
            'proceso_id' => $procesoID,
            'programa_id' => $programaID,
        ]);
    }

    public function obtenerEstudiantePorCodigo(string|array $codigosEstudiante): array
    {
        if (empty($codigosEstudiante)) {
            return [];
        }

        $bindings = [];
        $whereClause = '';

        if (is_array($codigosEstudiante)) {
            $placeholders = [];
            foreach ($codigosEstudiante as $index => $codigo) {
                $key = ":codigo{$index}";
                $bindings[$key] = $codigo;
                $placeholders[] = $key;
            }
            $whereClause = 'IN (' . implode(',', $placeholders) . ')';
        } else {
            $bindings = [':codigo' => $codigosEstudiante];
            $whereClause = '= :codigo';
        }

        $sql = "
            SELECT 
                ESTP.ESTP_CODIGOMATRICULA,
                CLAVE2.TOTALCREDITOSPENSUM AS PENSUM_ESTUD, 
                ESTP.ESTP_PERIODOACADEMICO AS UBICACION_SEMESTRAL,
                SITE.SITE_DESCRIPCION AS SITUACION,
                CATE.CATE_DESCRIPCION AS CATEGORIA,
                (CLAVE2.TOTALCREDITOSPENSUM - ESTP.ESTP_CREDITOSAPROBADOS) AS CRED_PENDIENTES,
                PEGE.PEGE_DOCUMENTOIDENTIDAD AS DOCUMENTO,
                PEGE.PEGE_LUGAREXPEDICION AS LUGAR_EXPEDICION,
                PEGE.PEGE_TELEFONO AS TELEFONO,
                TIDG.TIDG_DESCRIPCION AS TIPO_DOCUMENTO_NOMBRE,
                TIDG.TIDG_ID AS TIPO_DOCUMENTO_ID,
                PENG.PENG_PRIMERAPELLIDO || ' ' || PENG.PENG_SEGUNDOAPELLIDO || ' ' ||
                PENG.PENG_PRIMERNOMBRE || ' ' || PENG.PENG_SEGUNDONOMBRE AS NOMBRES,
                PENG.PENG_PRIMERAPELLIDO AS PRIMER_APELLIDO,
                PENG.PENG_SEGUNDOAPELLIDO AS SEGUNDO_APELLIDO,
                PENG.PENG_PRIMERNOMBRE AS PRIMER_NOMBRE, 
                PENG.PENG_SEGUNDONOMBRE AS SEGUNDO_NOMBRE,
                PENG.PENG_SEXO AS GENERO,                
                PENG.PENG_EMAILINSTITUCIONAL AS EMAIL_INSTITUCIONAL                
            FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
            INNER JOIN ACADEMICO.PERSONAGENERAL PEGE ON ESTP.PEGE_ID = PEGE.PEGE_ID 
            INNER JOIN GENERAL.PERSONANATURALGENERAL PENG ON PEGE.PEGE_ID = PENG.PEGE_ID
            INNER JOIN GENERAL.TIPODOCUMENTOGENERAL TIDG ON TIDG.TIDG_ID = PEGE.TIDG_ID
            JOIN ACADEMICO.CATEGORIA CATE ON ESTP.CATE_ID = CATE.CATE_ID
            JOIN ACADEMICO.SITUACIONESTUDIANTE SITE ON ESTP.SITE_ID = SITE.SITE_ID
            LEFT JOIN (
                SELECT ESTP_ID, PENS_TOTALCREDITOS AS TOTALCREDITOSPENSUM,
                    SUM(MATE_PONDERACIONACADEMICA) AS CLAVE2PONDERA,
                    PENS_PONMINMATNOR AS PONDERACIONBASICA
                FROM (
                    SELECT ESTP.ESTP_ID, PENS.PENS_TOTALCREDITOS, MATE.MATE_PONDERACIONACADEMICA,
                        PENS.PENS_PONMINMATNOR
                    FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
                    JOIN ACADEMICO.PENSUM PENS ON ESTP.PENS_ID = PENS.PENS_ID
                    JOIN ACADEMICO.PENSUMMATERIA PEMA ON PENS.PENS_ID = PEMA.PENS_ID
                    JOIN ACADEMICO.MATERIA MATE ON PEMA.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
                    JOIN ACADEMICO.REGISTROACADEMICO REAC ON REAC.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
                                                        AND REAC.ESTP_ID = ESTP.ESTP_ID
                    WHERE PEMA.CICU_ID = 4
                    AND REAC.REAC_APROBADO = 1
                    AND PENS.TIPA_ID = 2
                    AND (ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS - 1
                        OR ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS)
                )
                GROUP BY ESTP_ID, PENS_TOTALCREDITOS, PENS_PONMINMATNOR
            ) CLAVE2 ON ESTP.ESTP_ID = CLAVE2.ESTP_ID

            WHERE ESTP.ESTP_CODIGOMATRICULA $whereClause
        ";

        return DB::connection('oracle_academico')->select($sql, $bindings);
    }

   public function quitarEstudiante(int $estudianteProcesoProgramaID)
    {
        try {
            $registro = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES')
                ->where('ppes_id', $estudianteProcesoProgramaID)
                ->first();

            if (!$registro) {
                return ;
            }

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES')
                ->where('ppes_id', $estudianteProcesoProgramaID)
                ->delete();

        } catch (\Throwable $e) {
            Log::info($e->getMessage);
        }
    }

}
