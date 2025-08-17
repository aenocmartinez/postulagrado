<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Src\domain\proceso\documento\ProcesoDocumento;
use Src\domain\proceso\documento\valueObject\NombreDocumento;
use Src\domain\proceso\documento\valueObject\ProcesoDocumentoId;
use Src\domain\proceso\documento\valueObject\ProcesoId;
use Src\domain\proceso\documento\valueObject\RutaDocumento;
use Src\domain\repositories\ProcesoDocumentoRepository;

class OracleProcesoDocumentoRepository extends Model implements ProcesoDocumentoRepository
{
    protected $connection = 'oracle_academpostulgrado';
    protected $table = 'ACADEMPOSTULGRADO.PROCESO_DOCUMENTO';
    protected $primaryKey = 'PRDO_ID';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['PROC_ID', 'PRDO_NOMBRE', 'PRDO_RUTA', 'PRDO_REGISTRADOPOR', 'PRDO_FECHACAMBIO'];

    public function crear(ProcesoDocumento $documento): bool
    {
        $cn = DB::connection('oracle_academpostulgrado');

        try {
            $cn->beginTransaction();

            $row = $cn->selectOne('SELECT ACADEMPOSTULGRADO.S_PROCESO_DOCUMENTO_ID.NEXTVAL AS ID FROM DUAL');
            $nuevoID = (int) ($row->ID ?? $row->id ?? 0);
            if ($nuevoID <= 0) {
                throw new \RuntimeException('No se pudo obtener el NEXTVAL de la secuencia.');
            }

            $cn->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')->insert([
                'PRDO_ID'             => $nuevoID,
                'PROC_ID'             => $documento->procesoId()->value(),
                'PRDO_NOMBRE'         => $documento->nombre()->value(),
                'PRDO_RUTA'           => $documento->ruta()->value(),
                'PRDO_REGISTRADOPOR'  => (string) (Auth::id() ?? 'system'),
                'PRDO_FECHACAMBIO'    => DB::raw('SYSTIMESTAMP'),
            ]);

            $documento->setId(new ProcesoDocumentoId($nuevoID));

            Cache::forget('documentos_proceso_' . $documento->procesoId()->value());

            $cn->commit();
            return true;

        } catch (\Throwable $e) {
            
            try {
                 $cn->rollBack(); 
            } catch (\Throwable $ignore) {}

            Log::error('Error al crear documento de proceso: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public function buscarPorID(int $id): ProcesoDocumento
    {
        try {
            $row = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->select('PRDO_ID', 'PROC_ID', 'PRDO_NOMBRE', 'PRDO_RUTA')
                ->where('PRDO_ID', $id)
                ->first();

            if ($row) {
                return new ProcesoDocumento(
                    new ProcesoDocumentoId((int)($row->PRDO_ID ?? $row->prdo_id)),
                    new ProcesoId((int)($row->PROC_ID ?? $row->proc_id)),
                    new NombreDocumento((string)($row->PRDO_NOMBRE ?? $row->prdo_nombre)),
                    new RutaDocumento((string)($row->PRDO_RUTA ?? $row->prdo_ruta))
                );
            }

        } catch (\Throwable $e) {
            Log::error("Error al buscar documento de proceso por ID {$id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        return new ProcesoDocumento(
            null,                             
            new ProcesoId(0),                 
            new NombreDocumento(''),          
            new RutaDocumento('documento')    
        );
    }

    public function listarDocumentosPorProceso(int $procesoID): array
    {
        $cacheKey = 'documentos_proceso_' . $procesoID;

        return Cache::remember($cacheKey, now()->addHours(4), function () use ($procesoID) {
            $documentos = [];

            try {
                $rows = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                    ->select('PRDO_ID', 'PROC_ID', 'PRDO_NOMBRE', 'PRDO_RUTA', 'PRDO_FECHACAMBIO')
                    ->where('PROC_ID', $procesoID)
                    ->orderBy('PRDO_FECHACAMBIO', 'desc')
                    ->get();

                foreach ($rows as $row) {
                    $data = [
                        'id'         => (int)   ($row->PRDO_ID     ?? $row->prdo_id ?? 0),
                        'proceso_id' => (int)   ($row->PROC_ID     ?? $row->proc_id ?? $procesoID),
                        'nombre'     => (string)($row->PRDO_NOMBRE ?? $row->prdo_nombre ?? ''),
                        'ruta'       => (string)($row->PRDO_RUTA   ?? $row->prdo_ruta ?? ''),
                    ];

                    $documentos[] = ProcesoDocumento::fromPrimitives($data);
                }
            } catch (\Throwable $e) {
                Log::error("Error al listar documentos del proceso ID {$procesoID}: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }

            return $documentos;
        });
    }

    public function eliminar(int $documentoID): bool
    {
        $cn = DB::connection('oracle_academpostulgrado');

        try {
            $cn->beginTransaction();
            $row = $cn->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->select('PROC_ID')
                ->where('PRDO_ID', $documentoID)
                ->first();

            if (!$row) {
                $cn->rollBack();
                return false;
            }

            $procesoID = (int) ($row->PROC_ID ?? $row->proc_id ?? 0);
            $deleted = $cn->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->where('PRDO_ID', $documentoID)
                ->delete();

            if ($deleted <= 0) {
                $cn->rollBack();
                return false;
            }

            $cn->commit();

            if ($procesoID > 0) {
                Cache::forget('documentos_proceso_' . $procesoID);
            }

            return true;

        } catch (\Throwable $e) {
            
            try { 
                $cn->rollBack(); 
            } catch (\Throwable $ignore) {}

            Log::error("Error al eliminar documento de proceso ID {$documentoID}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            return false;
        }
    }
}
