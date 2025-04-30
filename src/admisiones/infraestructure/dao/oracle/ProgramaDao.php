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
use Src\admisiones\domain\Estudiante;

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
        return Cache::remember('programa_' . $programaID, now()->addHours(4), function () use ($programaID) {
            $programa = new Programa(
                FabricaDeRepositorios::getInstance()->getProgramaRepository()
            );
    
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
                Log::error("Error al buscar programas por nivel educativo '{$nivelEducativoID}': " . $e->getMessage());
            }
    
            return $programas;
        });
    }    

    public function listarEstudiantesCandidatosAGrado(int $codigoPrograma, int $periodoAnio, int $periodoNumero): array {

        $cacheKey = "estudiantes_candidatos_{$codigoPrograma}_{$periodoAnio}_{$periodoNumero}";

        return Cache::remember($cacheKey, now()->addHours(4), function () use ($codigoPrograma, $periodoAnio, $periodoNumero) {

            $estudiantes = [];
        
            try {
                $sql = <<<SQL
                    SELECT DISTINCT
                    PROG.PROG_NOMBRE AS programa_academico,
                    CLAVE2.PENSUMNOMBRE AS pensum_estud,
                    ESTP.ESTP_CODIGOMATRICULA AS codigo_est,
                    PEGE.PEGE_DOCUMENTOIDENTIDAD AS documento,
                    PENG.PENG_PRIMERAPELLIDO ||' '|| PENG.PENG_SEGUNDOAPELLIDO ||' '||PENG.PENG_PRIMERNOMBRE ||' '|| PENG.PENG_SEGUNDONOMBRE AS nombres,
                    ESTP.ESTP_PERIODOACADEMICO AS ubicacion_semestral,
                    CATE.CATE_DESCRIPCION AS categoria,
                    SITE.SITE_DESCRIPCION AS situacion,
                    CLAVE2.TOTALCREDITOSPENSUM AS total_creditos_pensum,
                    (CLAVE2.TOTALCREDITOSPENSUM - ESTP.ESTP_CREDITOSAPROBADOS) AS cred_pendientes,
                    CLAVE2.PONDERACIONBASICA AS area_basica_pensum,
                    CLAVE2.CLAVE2PONDERA AS cred_aprob_area_basica,
                    (CLAVE2.PONDERACIONBASICA - CLAVE2.CLAVE2PONDERA ) AS cr_pendientes_area_basica,
                    CLAVE5.AREAPROFUNDIZACIONPROGRAMA AS total_cred_profund_pens,
                    CLAVE1.CLAVE1PONDERA AS cred_aprob_profundizacion,
                    (CLAVE5.AREAPROFUNDIZACIONPROGRAMA-CLAVE1.CLAVE1PONDERA) AS cr_pend_profundizacion,
                    CLAVE4.AREACOMPLEMENTARIAPROGRAMA AS area_comple_pens,
                    CLAVE3.CLAVE3PONDERA AS cred_aprob_electiva_comple,
                    (CLAVE4.AREACOMPLEMENTARIAPROGRAMA-CLAVE3.CLAVE3PONDERA) AS cr_pend_electiva_comple
                        
                    FROM
                        ACADEMICO.ESTUDIANTEPENSUM ESTP,
                        ACADEMICO.PROGRAMA PROG,
                        ACADEMICO.UNIDADPROGRAMA UNPR,
                        GENERAL.PERSONAGENERAL PEGE,
                        ACADEMICO.PERIODOUNIVERSIDAD PEUN,
                        GENERAL.PERSONANATURALGENERAL PENG,
                        ACADEMICO.CATEGORIA CATE,
                        ACADEMICO.SITUACIONESTUDIANTE SITE,
                        (
                            SELECT
                                    ESTP.ESTP_ID,
                                SUM(REAC.REAC_PONDERACIONACADEMICA) AS CLAVE1PONDERA
                                    FROM
                                ACADEMICO.ESTUDIANTEPENSUM ESTP,
                                ACADEMICO.REGISTROACADEMICO REAC
                            WHERE
                                ESTP.ESTP_ID=REAC.ESTP_ID
                            AND REAC.REAC_APROBADO=1
                            AND REAC.REAC_CUENTAPROMEDIO=1
                            AND (
                                
                                REAC.MATE_CODIGOMATERIA LIKE '%15'
                                OR  REAC.MATE_CODIGOMATERIA LIKE '%16'
                                OR  REAC.MATE_CODIGOMATERIA LIKE '%17'
                                OR  REAC.MATE_CODIGOMATERIA LIKE '%18'
                                OR  REAC.MATE_CODIGOMATERIA LIKE '%19'
                                OR  REAC.MATE_CODIGOMATERIA LIKE '%20')
                        
                            GROUP BY 
                                    ESTP.ESTP_ID) CLAVE1, 
                        (
                            SELECT
                                    ESTP.ESTP_ID,
                                SUM(REAC.REAC_PONDERACIONACADEMICA) AS CLAVE3PONDERA
                                    FROM
                                ACADEMICO.ESTUDIANTEPENSUM ESTP,
                                ACADEMICO.REGISTROACADEMICO REAC
                            WHERE
                                ESTP.ESTP_ID=REAC.ESTP_ID
                            AND REAC.REAC_APROBADO=1
                            AND REAC.REAC_CUENTAPROMEDIO=1
                            AND REAC.MATE_CODIGOMATERIA LIKE '%14'           
                            GROUP BY 
                                    ESTP.ESTP_ID) CLAVE3, 
                        (
                            SELECT DISTINCT
                                ESTP.ESTP_ID,
                                PENS.PENS_TOTALCREDITOS              AS TOTALCREDITOSPENSUM,
                                SUM (MATE.MATE_PONDERACIONACADEMICA) AS CLAVE2PONDERA,
                                PENS.PENS_PONMINMATNOR AS PONDERACIONBASICA,
                                PENS.PENS_DESCRIPCION AS PENSUMNOMBRE
                            FROM
                                ACADEMICO.ESTUDIANTEPENSUM ESTP,
                                ACADEMICO.PENSUM PENS,
                                ACADEMICO.PENSUMMATERIA PEMA,
                                ACADEMICO.MATERIA MATE,
                                ACADEMICO.REGISTROACADEMICO REAC
                            WHERE
                                ESTP.PENS_ID=PENS.PENS_ID
                            AND REAC.ESTP_ID=ESTP.ESTP_ID
                            AND MATE.MATE_CODIGOMATERIA=PEMA.MATE_CODIGOMATERIA
                            AND MATE.MATE_CODIGOMATERIA=REAC.MATE_CODIGOMATERIA
                            AND PENS.PENS_ID=PEMA.PENS_ID
                            AND PEMA.CICU_ID=4 
                            AND REAC.REAC_APROBADO=1
                            AND PENS.TIPA_ID=2
                        AND (ESTP.ESTP_PERIODOACADEMICO=PENS.PENS_NUMPERIODOS-1)
                            GROUP BY
                                ESTP.ESTP_ID,
                                PENS.PENS_TOTALCREDITOS,
                                PENS.PENS_PONMINMATNOR,
                                PENS.PENS_DESCRIPCION
                        UNION
                                SELECT DISTINCT
                                ESTP.ESTP_ID,
                                PENS.PENS_TOTALCREDITOS              AS TOTALCREDITOSPENSUM,
                                SUM (MATE.MATE_PONDERACIONACADEMICA) AS CLAVE2PONDERA,
                                PENS.PENS_PONMINMATNOR AS PONDERACIONBASICA,
                                PENS.PENS_DESCRIPCION AS PENSUMNOMBRE
                            FROM
                                ACADEMICO.ESTUDIANTEPENSUM ESTP,
                                ACADEMICO.PENSUM PENS,
                                ACADEMICO.PENSUMMATERIA PEMA,
                                ACADEMICO.MATERIA MATE,
                                ACADEMICO.REGISTROACADEMICO REAC
                            WHERE
                                ESTP.PENS_ID=PENS.PENS_ID
                            AND REAC.ESTP_ID=ESTP.ESTP_ID
                            AND MATE.MATE_CODIGOMATERIA=PEMA.MATE_CODIGOMATERIA
                            AND MATE.MATE_CODIGOMATERIA=REAC.MATE_CODIGOMATERIA
                            AND PENS.PENS_ID=PEMA.PENS_ID
                            AND PEMA.CICU_ID=4 
                            AND REAC.REAC_APROBADO=1
                            AND PENS.TIPA_ID=2
                        AND (ESTP.ESTP_PERIODOACADEMICO=PENS.PENS_NUMPERIODOS)
                            GROUP BY
                                ESTP.ESTP_ID,
                                PENS.PENS_TOTALCREDITOS,
                                PENS.PENS_PONMINMATNOR,
                                PENS.PENS_DESCRIPCION
                                        
                                        
                                ) CLAVE2,
                    
                    
                    
                    (SELECT DISTINCT ESTP.ESTP_ID,SUM(MATE.MATE_PONDERACIONACADEMICA) AREACOMPLEMENTARIAPROGRAMA
                    FROM 
                    ACADEMICO.PENSUMMATERIA PEMA,
                    ACADEMICO.CAMPOFORMACION CAFO,
                    ACADEMICO.MATERIA MATE, 
                    ACADEMICO.PENSUM PENS,
                    ACADEMICO.ESTUDIANTEPENSUM ESTP
                    WHERE 
                    PEMA.CAFO_ID =CAFO.CAFO_ID
                    AND PEMA.PENS_ID=PENS.PENS_ID
                    AND ESTP.PENS_ID=PENS.PENS_ID
                    AND MATE.MATE_CODIGOMATERIA=PEMA.MATE_CODIGOMATERIA
                    AND PEMA.CAFO_ID=22 
                    GROUP BY
                    ESTP.ESTP_ID) CLAVE4,
                    
                    (SELECT DISTINCT ESTP.ESTP_ID,SUM(MATE.MATE_PONDERACIONACADEMICA) AREAPROFUNDIZACIONPROGRAMA
                    FROM 
                    ACADEMICO.PENSUMMATERIA PEMA,
                    ACADEMICO.CAMPOFORMACION CAFO,
                    ACADEMICO.MATERIA MATE, 
                    ACADEMICO.PENSUM PENS,
                    ACADEMICO.ESTUDIANTEPENSUM ESTP
                    WHERE 
                    PEMA.CAFO_ID =CAFO.CAFO_ID
                    AND PEMA.PENS_ID=PENS.PENS_ID
                    AND ESTP.PENS_ID=PENS.PENS_ID
                    AND MATE.MATE_CODIGOMATERIA=PEMA.MATE_CODIGOMATERIA
                    AND PEMA.CAFO_ID=21
                    GROUP BY
                    ESTP.ESTP_ID) CLAVE5
                    
                    
                    WHERE
                    ESTP.ESTP_ID=CLAVE1.ESTP_ID (+)
                    AND ESTP.ESTP_ID (+)=CLAVE2.ESTP_ID
                    AND ESTP.ESTP_ID=CLAVE3.ESTP_ID (+)
                    AND ESTP.ESTP_ID=CLAVE4.ESTP_ID (+)
                    AND ESTP.ESTP_ID=CLAVE5.ESTP_ID (+)
                    AND ESTP.UNPR_ID=UNPR.UNPR_ID
                    AND PROG.PROG_ID=UNPR.PROG_ID
                    AND PROG.PROG_CODIGOPROGRAMA = :codigoPrograma
                    AND PEGE.PEGE_ID=PENG.PEGE_ID
                    AND PEGE.PEGE_ID=ESTP.PEGE_ID
                    AND ESTP.CATE_ID=CATE.CATE_ID
                    AND ESTP.SITE_ID=SITE.SITE_ID
                    AND ESTP.PEUN_ID=PEUN.PEUN_ID
                    AND PEUN.PEUN_ANO = :anio
                    AND PEUN.PEUN_PERIODO = :periodo
                    AND PEUN.TPPA_ID=1
                    ORDER BY
                        6 DESC, 5 DESC, 4 DESC  
                SQL;
        
                $registros = DB::connection('oracle_academico')->select($sql, [
                    'codigoPrograma' => $codigoPrograma,
                    'anio' => $periodoAnio,
                    'periodo' => $periodoNumero
                ]);
        
                foreach ($registros as $r) {
                    $e = new Estudiante();
                    $e->setPensum($r->pensum_estud ?? '');
                    $e->setCodigo($r->codigo_est);
                    $e->setDocumento($r->documento);
                    $e->setNombre($r->nombres);
                    $e->setUbicacionSemestre($r->ubicacion_semestral);
                    $e->setCategoria($r->categoria);
                    $e->setSituacion($r->situacion);
                    $e->setTotalCreditosPensum((int) $r->total_creditos_pensum);
                    $e->setNumeroCreditosPendientes((int) $r->cred_pendientes);
                    $e->setNumeroCreditosAreaBasica((int) $r->area_basica_pensum);
                    $e->setNumeroCreditosAprobadosAreaBasica((int) $r->cred_aprob_area_basica);
                    $e->setNumeroCreditosPendientesAreaBasica((int) $r->cr_pendientes_area_basica);
                    $e->setNumeroCreditosAreaProfundizacion((int) $r->total_cred_profund_pens);
                    $e->setNumeroCreditosAprobadosAreaProfundizacion((int) $r->cred_aprob_profundizacion);
                    $e->setNumeroCreditosPendientesAreaProfundizacion((int) $r->cr_pend_profundizacion);
                    $e->setNumeroCreditosElectivos((int) $r->area_comple_pens);
                    $e->setNumeroCreditosAprobadosElectivos((int) $r->cred_aprob_electiva_comple);
                    $e->setNumeroCreditosPendientesElectivos((int) $r->cr_pend_electiva_comple);
                    $estudiantes[] = $e;
                }
            } catch (\Exception $e) {
                Log::error("Error al listar estudiantes candidatos a grado: " . $e->getMessage());
            }
        
            return $estudiantes;
        });
    }    
}
