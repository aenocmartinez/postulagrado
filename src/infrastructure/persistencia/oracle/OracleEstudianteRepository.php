<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\DB;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\domain\repositories\EstudianteRepository;

class OracleEstudianteRepository implements EstudianteRepository 
{
    // public function buscarEstudiantePorCodigo(string|array $parametros): array
    // {
    //     if (empty($parametros)) {
    //         return [];
    //     }

    //     $bindings = [];
    //     $condiciones = [];

    //     if (is_array($parametros)) {
    //         $placeholders = [];

    //         foreach ($parametros as $index => $valor) {
    //             $key = ":valor{$index}";
    //             $bindings[$key] = $valor;
    //             $placeholders[] = $key;
    //         }

    //         $whereClause = '(' .
    //             'ESTP.ESTP_CODIGOMATRICULA IN (' . implode(',', $placeholders) . ') ' .
    //             'OR PEGE.PEGE_DOCUMENTOIDENTIDAD IN (' . implode(',', $placeholders) . ')' .
    //             ')';
    //     } else {
    //         $bindings = [':valor' => $parametros];
    //         $whereClause = '(' .
    //             'ESTP.ESTP_CODIGOMATRICULA = :valor ' .
    //             'OR PEGE.PEGE_DOCUMENTOIDENTIDAD = :valor' .
    //             ')';
    //     }

    //     $sql = "
    //         SELECT 
    //             ESTP.ESTP_CODIGOMATRICULA,
    //             CLAVE2.TOTALCREDITOSPENSUM AS PENSUM_ESTUD, 
    //             ESTP.ESTP_PERIODOACADEMICO AS UBICACION_SEMESTRAL,
    //             SITE.SITE_DESCRIPCION AS SITUACION,
    //             CATE.CATE_DESCRIPCION AS CATEGORIA,
    //             (CLAVE2.TOTALCREDITOSPENSUM - ESTP.ESTP_CREDITOSAPROBADOS) AS CRED_PENDIENTES,
    //             PEGE.PEGE_DOCUMENTOIDENTIDAD AS DOCUMENTO,
    //             PEGE.PEGE_LUGAREXPEDICION AS LUGAR_EXPEDICION,
    //             PEGE.PEGE_TELEFONO AS TELEFONO,
    //             TIDG.TIDG_DESCRIPCION AS TIPO_DOCUMENTO_NOMBRE,
    //             TIDG.TIDG_ID AS TIPO_DOCUMENTO_ID,
    //             PENG.PENG_PRIMERAPELLIDO || ' ' || PENG.PENG_SEGUNDOAPELLIDO || ' ' ||
    //             PENG.PENG_PRIMERNOMBRE || ' ' || PENG.PENG_SEGUNDONOMBRE AS NOMBRES,
    //             PENG.PENG_PRIMERAPELLIDO AS PRIMER_APELLIDO,
    //             PENG.PENG_SEGUNDOAPELLIDO AS SEGUNDO_APELLIDO,
    //             PENG.PENG_PRIMERNOMBRE AS PRIMER_NOMBRE, 
    //             PENG.PENG_SEGUNDONOMBRE AS SEGUNDO_NOMBRE,
    //             PENG.PENG_SEXO AS GENERO,                
    //             PENG.PENG_EMAILINSTITUCIONAL AS EMAIL_INSTITUCIONAL  
    //         FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
    //         INNER JOIN ACADEMICO.PERSONAGENERAL PEGE ON ESTP.PEGE_ID = PEGE.PEGE_ID 
    //         INNER JOIN GENERAL.PERSONANATURALGENERAL PENG ON PEGE.PEGE_ID = PENG.PEGE_ID
    //         INNER JOIN GENERAL.TIPODOCUMENTOGENERAL TIDG ON TIDG.TIDG_ID = PEGE.TIDG_ID
    //         JOIN ACADEMICO.CATEGORIA CATE ON ESTP.CATE_ID = CATE.CATE_ID
    //         JOIN ACADEMICO.SITUACIONESTUDIANTE SITE ON ESTP.SITE_ID = SITE.SITE_ID
    //         LEFT JOIN (
    //             SELECT ESTP_ID, PENS_TOTALCREDITOS AS TOTALCREDITOSPENSUM,
    //                 SUM(MATE_PONDERACIONACADEMICA) AS CLAVE2PONDERA,
    //                 PENS_PONMINMATNOR AS PONDERACIONBASICA
    //             FROM (
    //                 SELECT ESTP.ESTP_ID, PENS.PENS_TOTALCREDITOS, MATE.MATE_PONDERACIONACADEMICA,
    //                     PENS.PENS_PONMINMATNOR
    //                 FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
    //                 JOIN ACADEMICO.PENSUM PENS ON ESTP.PENS_ID = PENS.PENS_ID
    //                 JOIN ACADEMICO.PENSUMMATERIA PEMA ON PENS.PENS_ID = PEMA.PENS_ID
    //                 JOIN ACADEMICO.MATERIA MATE ON PEMA.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
    //                 JOIN ACADEMICO.REGISTROACADEMICO REAC ON REAC.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
    //                                                     AND REAC.ESTP_ID = ESTP.ESTP_ID
    //                 WHERE PEMA.CICU_ID = 4
    //                 AND REAC.REAC_APROBADO = 1
    //                 AND PENS.TIPA_ID = 2
    //                 AND (ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS - 1
    //                     OR ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS)
    //             )
    //             GROUP BY ESTP_ID, PENS_TOTALCREDITOS, PENS_PONMINMATNOR
    //         ) CLAVE2 ON ESTP.ESTP_ID = CLAVE2.ESTP_ID

    //         WHERE $whereClause
    //     ";

    //     return DB::connection('oracle_academico')->select($sql, $bindings);
    // }

    public function buscarEstudiantePorCodigo(string|array $parametros): array
    {
        if (empty($parametros)) {
            return [];
        }

        // SELECT base con JOIN a ESTUDIANTE_DATOS (columnas reales del DDL)
        $selectBase = <<<SQL
        SELECT DISTINCT 
            ESTP.ESTP_CODIGOMATRICULA                  AS estp_codigomatricula,
            CLAVE2.TOTALCREDITOSPENSUM                 AS pensum_estud,
            ESTP.ESTP_PERIODOACADEMICO                 AS ubicacion_semestral,
            SITE.SITE_DESCRIPCION                      AS situacion,
            CATE.CATE_DESCRIPCION                      AS categoria,
            (CLAVE2.TOTALCREDITOSPENSUM - ESTP.ESTP_CREDITOSAPROBADOS) AS cred_pendientes,
            ESTP.ESTP_CREDITOSAPROBADOS                AS cred_aprobados,

            PEGE.PEGE_DOCUMENTOIDENTIDAD               AS documento,
            PEGE.PEGE_LUGAREXPEDICION                  AS lugar_expedicion,
            PEGE.PEGE_TELEFONO                         AS telefono,
            TIDG.TIDG_DESCRIPCION                      AS tipo_documento_nombre,
            TIDG.TIDG_ID                               AS tipo_documento_id,

            PENG.PENG_PRIMERAPELLIDO || ' ' || PENG.PENG_SEGUNDOAPELLIDO || ' ' ||
            PENG.PENG_PRIMERNOMBRE  || ' ' || PENG.PENG_SEGUNDONOMBRE   AS nombres,
            PENG.PENG_PRIMERAPELLIDO                   AS primer_apellido,
            PENG.PENG_SEGUNDOAPELLIDO                  AS segundo_apellido,
            PENG.PENG_PRIMERNOMBRE                     AS primer_nombre,
            PENG.PENG_SEGUNDONOMBRE                    AS segundo_nombre,
            PENG.PENG_SEXO                             AS genero,
            PENG.PENG_EMAILINSTITUCIONAL               AS email_institucional,

            -- Datos del formulario (si existen) - columnas reales
            ED.CORREO_ELECTRONICO_PERSONAL             AS correo,
            ED.TELEFONO                                AS telefono_form,
            ED.PATH_DOCUMENTO_IDENTIDAD                AS documento_url,
            ED.UNIVERSIDAD_PREGRADO                    AS universidad_pregrado,
            ED.TITULO_PREGRADO                         AS titulo_pregrado,
            TO_CHAR(ED.FECHA_GRADO_PREGRADO, 'YYYY-MM-DD') AS fecha_grado_pregrado,
            NVL2(ED.ACEN_ID, 1, 0)                     AS formulario_actualizado,
            CASE ED.ES_HIJO_FUNCIONARIO        WHEN 'S' THEN 1 ELSE 0 END AS hijo_funcionario,
            CASE ED.ES_HIJO_DOCENTE            WHEN 'S' THEN 1 ELSE 0 END AS hijo_docente,
            CASE ED.ES_FUNCIONARIO_UNIVERSIDAD WHEN 'S' THEN 1 ELSE 0 END AS funcionario_universidad,
            CASE ED.ES_DOCENTE_UNIVERSIDAD     WHEN 'S' THEN 1 ELSE 0 END AS docente_universidad,
            CASE ED.GRUPO_INVESTIGACION_PERTENECE WHEN 'S' THEN 1 ELSE 0 END AS grupo_investigacion_pertenece,
            ED.CODIGO_SABER_PRO                          AS codigo_saber_pro,
            ED.PATH_CERTIFICADO_SABER_PRO                AS certificado_saber_pro_url,
            ED.GRUPO_INVESTIGACION_NOMBRE               AS grupo_investigacion_nombre,
            ED.DEPARTAMENTO                             AS departamento,
            ED.CIUDAD                                   AS ciudad,
            ED.DIRECCION                                AS direccion

        FROM ACADEMICO.ESTUDIANTEPENSUM ESTP
        JOIN ACADEMICO.PERSONAGENERAL PEGE
        ON ESTP.PEGE_ID = PEGE.PEGE_ID 
        JOIN GENERAL.PERSONANATURALGENERAL PENG
        ON PEGE.PEGE_ID = PENG.PEGE_ID
        JOIN GENERAL.TIPODOCUMENTOGENERAL TIDG
        ON TIDG.TIDG_ID = PEGE.TIDG_ID
        JOIN ACADEMICO.CATEGORIA CATE
        ON ESTP.CATE_ID = CATE.CATE_ID
        JOIN ACADEMICO.SITUACIONESTUDIANTE SITE
        ON ESTP.SITE_ID = SITE.SITE_ID
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
                JOIN ACADEMICO.REGISTROACADEMICO REAC
                ON REAC.MATE_CODIGOMATERIA = MATE.MATE_CODIGOMATERIA
                AND REAC.ESTP_ID = ESTP.ESTP_ID
                WHERE PEMA.CICU_ID = 4
                AND REAC.REAC_APROBADO = 1
                AND PENS.TIPA_ID = 2
                AND (ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS - 1
                    OR ESTP.ESTP_PERIODOACADEMICO = PENS.PENS_NUMPERIODOS)
            )
            GROUP BY ESTP_ID, PENS_TOTALCREDITOS, PENS_PONMINMATNOR
        ) CLAVE2
        ON ESTP.ESTP_ID = CLAVE2.ESTP_ID
        LEFT JOIN ACADEMPOSTULGRADO.ESTUDIANTE_DATOS ED
        ON ED.ESTU_CODIGO = ESTP.ESTP_CODIGOMATRICULA
        WHERE %s
        SQL;

        $bindings = [];
        if (is_array($parametros)) {
            $phCod = [];
            $phDoc = [];
            foreach (array_values($parametros) as $i => $v) {
                $phCod[] = ":c{$i}";
                $bindings[":c{$i}"] = $v;
                $phDoc[] = ":d{$i}";
                $bindings[":d{$i}"] = $v;
            }

            $where1 = 'ESTP.ESTP_CODIGOMATRICULA IN ('.implode(',', $phCod).')';
            $where2 = 'PEGE.PEGE_DOCUMENTOIDENTIDAD IN ('.implode(',', $phDoc).')';

            $sql = sprintf($selectBase, $where1)
                . ' UNION ALL '
                . sprintf($selectBase, $where2);
        } else {
            $bindings = [
                ':codigo'    => $parametros,
                ':documento' => $parametros,
            ];

            $where1 = 'ESTP.ESTP_CODIGOMATRICULA = :codigo';
            $where2 = 'PEGE.PEGE_DOCUMENTOIDENTIDAD = :documento';

            $sql = sprintf($selectBase, $where1)
                . ' UNION ALL '
                . sprintf($selectBase, $where2);
        }

        // Usa la conexión que tenga permisos sobre ambos esquemas
        return DB::connection('oracle_academpostulgrado')->select($sql, $bindings);
        // Si ya diste permisos desde oracle_academpostulgrado:
        // return DB::connection('oracle_academpostulgrado')->select($sql, $bindings);
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