<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\DB;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\domain\repositories\EstudianteRepository;

class OracleEstudianteRepository implements EstudianteRepository 
{
    public function buscarEstudiantePorCodigo(string|array $parametros): array
    {
        if (empty($parametros)) {
            return [];
        }

        $bindings = [];
        $condiciones = [];

        if (is_array($parametros)) {
            $placeholders = [];

            foreach ($parametros as $index => $valor) {
                $key = ":valor{$index}";
                $bindings[$key] = $valor;
                $placeholders[] = $key;
            }

            $whereClause = '(' .
                'ESTP.ESTP_CODIGOMATRICULA IN (' . implode(',', $placeholders) . ') ' .
                'OR PEGE.PEGE_DOCUMENTOIDENTIDAD IN (' . implode(',', $placeholders) . ')' .
                ')';
        } else {
            $bindings = [':valor' => $parametros];
            $whereClause = '(' .
                'ESTP.ESTP_CODIGOMATRICULA = :valor ' .
                'OR PEGE.PEGE_DOCUMENTOIDENTIDAD = :valor' .
                ')';
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

            WHERE $whereClause
        ";

        return DB::connection('oracle_academico')->select($sql, $bindings);
    }

    public function findPpesId(int $procesoId, int $programaId, string $codigo): ?int
    {
        $sql = "
            SELECT ppes.PPES_ID
            FROM ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES ppes
            JOIN ACADEMPOSTULGRADO.PROCESO_PROGRAMA pp
            ON pp.PROGR_ID = ppes.PROGR_ID
            WHERE pp.PROC_ID = :proceso_id
            AND pp.PROG_ID = :programa_id
            AND ppes.ESTU_CODIGO = :codigo
        ";

        $row = DB::connection('oracle_academpostulgrado')->selectOne($sql, [
            'proceso_id'  => $procesoId,
            'programa_id' => $programaId,
            'codigo'      => $codigo,
        ]);

        return $row?->ppes_id !== null ? (int)$row->ppes_id : null;
    }

    public function guardarDatosActualizados(ActualizacionDatosDTO $datos): bool
    {
        $sn = static fn (?string $v) => strtoupper((string)$v) === 'SI' ? 'S' : 'N';

        if ($datos->programa_id === null) {
            throw new \InvalidArgumentException('Falta programa_id en el DTO para organizar el storage.');
        }

        $baseDir = sprintf(
            'documentos_proceso/%d/%d/%s/%d',
            $datos->proceso_id,
            $datos->programa_id,
            $datos->codigo,
            $datos->enlace_id
        );

        $genNombre = static function (string $prefijo, \Illuminate\Http\UploadedFile $file): string {
            $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $rand = bin2hex(random_bytes(8));
            return "{$prefijo}_{$rand}.{$ext}";
        };

        $pathDoc = $datos->doc_identificacion
            ? $datos->doc_identificacion->storeAs($baseDir, $genNombre('doc_identificacion', $datos->doc_identificacion), 'public')
            : null;

        $pathCert = $datos->cert_saber
            ? $datos->cert_saber->storeAs($baseDir, $genNombre('cert_saber', $datos->cert_saber), 'public')
            : null;

        $bind = [
            'ACEN_ID'                        => $datos->enlace_id,
            'ESTU_CODIGO'                    => $datos->codigo,
            'PATH_DOCUMENTO_IDENTIDAD'       => $pathDoc,
            'PATH_CERTIFICADO_SABER_PRO'     => $pathCert,
            'CODIGO_SABER_PRO'               => $datos->codigo_saber,
            'GRUPO_INVESTIGACION_PERTENECE'  => $sn($datos->grupo_investigacion),
            'GRUPO_INVESTIGACION_NOMBRE'     => $datos->nombre_grupo,
            'CORREO_ELECTRONICO_PERSONAL'    => $datos->correo_personal,
            'TELEFONO'                       => $datos->telefono,
            'DEPARTAMENTO'                   => $datos->departamento,
            'CIUDAD'                         => $datos->ciudad,
            'DIRECCION'                      => $datos->direccion,
            'ES_HIJO_FUNCIONARIO'            => $sn($datos->hijo_funcionario),
            'ES_HIJO_DOCENTE'                => $sn($datos->hijo_docente),
            'ES_FUNCIONARIO_UNIVERSIDAD'     => $sn($datos->es_funcionario),
            'ES_DOCENTE_UNIVERSIDAD'         => $sn($datos->es_docente),
            'TITULO_PREGRADO'                => $datos->titulo_pregrado,
            'UNIVERSIDAD_PREGRADO'           => $datos->universidad_pregrado,
            'FECHA_GRADO_PREGRADO'           => $datos->fecha_grado_pregrado ?: null,
        ];

        $sqlInsert = <<<SQL
        INSERT INTO ACADEMPOSTULGRADO.ESTUDIANTE_DATOS (
            ACEN_ID, ESTU_CODIGO,
            PATH_DOCUMENTO_IDENTIDAD, PATH_CERTIFICADO_SABER_PRO, CODIGO_SABER_PRO,
            GRUPO_INVESTIGACION_PERTENECE, GRUPO_INVESTIGACION_NOMBRE,
            CORREO_ELECTRONICO_PERSONAL, TELEFONO,
            DEPARTAMENTO, CIUDAD, DIRECCION,
            ES_HIJO_FUNCIONARIO, ES_HIJO_DOCENTE, ES_FUNCIONARIO_UNIVERSIDAD, ES_DOCENTE_UNIVERSIDAD,
            TITULO_PREGRADO, UNIVERSIDAD_PREGRADO, FECHA_GRADO_PREGRADO
        ) VALUES (
            :ACEN_ID, :ESTU_CODIGO,
            :PATH_DOCUMENTO_IDENTIDAD, :PATH_CERTIFICADO_SABER_PRO, :CODIGO_SABER_PRO,
            :GRUPO_INVESTIGACION_PERTENECE, :GRUPO_INVESTIGACION_NOMBRE,
            :CORREO_ELECTRONICO_PERSONAL, :TELEFONO,
            :DEPARTAMENTO, :CIUDAD, :DIRECCION,
            :ES_HIJO_FUNCIONARIO, :ES_HIJO_DOCENTE, :ES_FUNCIONARIO_UNIVERSIDAD, :ES_DOCENTE_UNIVERSIDAD,
            :TITULO_PREGRADO, :UNIVERSIDAD_PREGRADO,
            CASE WHEN :FECHA_GRADO_PREGRADO IS NULL THEN NULL
                ELSE TO_DATE(:FECHA_GRADO_PREGRADO, 'YYYY-MM-DD') END
        )
        SQL;

        return DB::connection('oracle_academpostulgrado')->transaction(
            function () use ($sqlInsert, $bind, $datos): bool {
                $insertOk = DB::connection('oracle_academpostulgrado')
                    ->affectingStatement($sqlInsert, $bind) > 0;

                DB::connection('oracle_academpostulgrado')->update(
                    "UPDATE ACADEMPOSTULGRADO.ACTUALIZACION_ENLACE
                    SET ACEN_USADO = 'S', ACEN_FECHAUSO = SYSDATE
                    WHERE ACEN_ID = :id",
                    ['id' => $datos->enlace_id]
                );

                return $insertOk;
            }
        );
    }

    public function buscarEnlacePorID(int $enlaceID): ?object
    {
        $row = DB::connection('oracle_academpostulgrado')->selectOne(
            "SELECT ACEN_ID, ACEN_USADO, ACEN_FECHAEXPIRA
             FROM ACADEMPOSTULGRADO.ACTUALIZACION_ENLACE
             WHERE ACEN_ID = :id",
            ['id' => $enlaceID]
        );

        return $row ?: null;
    }

}