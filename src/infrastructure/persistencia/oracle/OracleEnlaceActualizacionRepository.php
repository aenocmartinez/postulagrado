<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\EnlaceActualizacion;
use Src\domain\repositories\EnlaceActualizacionRepository;

class OracleEnlaceActualizacionRepository extends Model implements EnlaceActualizacionRepository
{
    protected $table = 'ACADEMPOSTULGRADO.ACTUALIZACION_ENLACE';
    public $timestamps = false;
    private const CONN = 'oracle_academpostulgrado';

    public function guardar(EnlaceActualizacion $enlace): bool
    {
        try {
            return DB::connection(self::CONN)
                ->table($this->table)
                ->insert([
                    'PROC_ID'          => $enlace->getProcesoID(),
                    'ESTU_CODIGO'      => $enlace->getCodigoEstudiante(),
                    'ACEN_TOKEN'       => $enlace->getToken(),
                    'ACEN_USADO'       => $enlace->getUsado(), // 'S' | 'N'
                    'ACEN_FECHAEXPIRA' => $this->toOracleDate($enlace->getFechaExpira()),
                    // 'ACEN_FECHACREACION' => DB::raw('SYSDATE'), 
                ]);
        } catch (\Exception $e) {
            Log::error("Error al guardar enlace de actualización (PROC_ID {$enlace->getProcesoID()}, ESTU {$enlace->getCodigoEstudiante()}): ".$e->getMessage());
            return false;
        }
    }

    /** Busca por ID. */
    public function buscarPorId(int $id): ?EnlaceActualizacion
    {
        try {
            $row = DB::connection(self::CONN)
                ->table($this->table)
                ->where('ACEN_ID', $id)
                ->first();

            return $row ? $this->toDomain($row) : null;
        } catch (\Exception $e) {
            Log::error("Error al buscar enlace por ID {$id}: ".$e->getMessage());
            return null;
        }
    }

    /** Busca por token. */
    public function buscarPorToken(string $token): ?EnlaceActualizacion
    {
        try {
            $row = DB::connection(self::CONN)
                ->table($this->table)
                ->where('ACEN_TOKEN', $token)
                ->first();

            return $row ? $this->toDomain($row) : null;
        } catch (\Exception $e) {
            Log::error("Error al buscar enlace por token {$token}: ".$e->getMessage());
            return null;
        }
    }

    /** Busca por código de estudiante y proceso (último emitido). */
    public function buscarPorCodigoEstudianteYProceso(string $codigoEstudiante, int $procesoId): ?EnlaceActualizacion
    {
        try {
            $row = DB::connection(self::CONN)
                ->table($this->table)
                ->where('ESTU_CODIGO', $codigoEstudiante)
                ->where('PROC_ID', $procesoId)
                ->orderBy('ACEN_FECHACREACION', 'desc')
                ->first();

            return $row ? $this->toDomain($row) : null;
        } catch (\Exception $e) {
            Log::error("Error al buscar enlace por código {$codigoEstudiante} y proceso {$procesoId}: ".$e->getMessage());
            return null;
        }
    }

    /** Marca como usado (solo si estaba en 'N') y setea fecha de uso. */
    public function marcarComoUsado(string $token): bool
    {
        try {
            $affected = DB::connection(self::CONN)
                ->table($this->table)
                ->where('ACEN_TOKEN', $token)
                ->where('ACEN_USADO', 'N')
                ->update([
                    'ACEN_USADO'    => 'S',
                    'ACEN_FECHAUSO' => DB::raw('SYSDATE'),
                ]);

            return $affected > 0;
        } catch (\Exception $e) {
            Log::error("Error al marcar como usado token {$token}: ".$e->getMessage());
            return false;
        }
    }

    /** Elimina registros expirados (ACEN_FECHAEXPIRA < SYSDATE). Retorna cantidad. */
    public function eliminarExpirados(): int
    {
        try {
            return DB::connection(self::CONN)
                ->table($this->table)
                ->whereNotNull('ACEN_FECHAEXPIRA')
                ->whereRaw('ACEN_FECHAEXPIRA < SYSDATE')
                ->delete();
        } catch (\Exception $e) {
            Log::error("Error al eliminar enlaces expirados: ".$e->getMessage());
            return 0;
        }
    }

    private function toDomain(object $row): EnlaceActualizacion
    {
        $r = (array) $row;
        $g = static fn($k) => $r[$k] ?? $r[strtolower($k)] ?? $r[strtoupper($k)] ?? null;

        $e = new EnlaceActualizacion();
        $e->setId((int) $g('ACEN_ID'))
          ->setProcesoID((int) $g('PROC_ID'))
          ->setCodigoEstudiante((string) ($g('ESTU_CODIGO') ?? ''))
          ->setToken((string) ($g('ACEN_TOKEN') ?? ''))
          ->setUsado((string) ($g('ACEN_USADO') ?? 'N'))
          ->setFechaExpira($this->formatFecha($g('ACEN_FECHAEXPIRA')))
          ->setFechaUso($this->formatFecha($g('ACEN_FECHAUSO')));

        return $e;
    }

    private function toOracleDate(?string $value)
    {
        if (!$value) return null;
        return $value;
    }

    private function formatFecha($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value ? (string) $value : '';
    }
}
