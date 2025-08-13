<?php

namespace Src\infraestructure\dao\oracle;

use Src\repositories\EstudianteRepository;
use Illuminate\Support\Facades\DB;


class EstudianteDao implements EstudianteRepository 
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
                PENG.PENG_SEGUNDONOMBRE AS GENERO,
                PENG.PENG_SEXO AS SEGUNDO_NOMBRE,                
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

}