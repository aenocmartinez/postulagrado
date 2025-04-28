<?php

namespace Src\admisiones\infraestructure\dao\oracle;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Exception;

use Src\admisiones\domain\ProcesoDocumento;
use Src\admisiones\repositories\ProcesoDocumentoRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProcesoDocumentoDao extends Model implements ProcesoDocumentoRepository
{
    protected $connection = 'oracle_academpostulgrado';
    protected $table = 'ACADEMPOSTULGRADO.PROCESO_DOCUMENTO';
    protected $primaryKey = 'PRDO_ID';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['PROC_ID', 'PRDO_NOMBRE', 'PRDO_RUTA', 'PRDO_REGISTRADOPOR', 'PRDO_FECHACAMBIO'];

    public function crear(ProcesoDocumento $documento): bool
    {
        try {
            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_PROCESO_DOCUMENTO_ID.NEXTVAL AS id FROM DUAL')
                ->id;

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->insert([
                    'PRDO_ID'           => $nuevoID,
                    'PROC_ID'           => $documento->getProceso()->getId(),
                    'PRDO_NOMBRE'       => $documento->getNombre(),
                    'PRDO_RUTA'         => $documento->getRuta(),
                    'PRDO_REGISTRADOPOR'=> Auth::user()->id ?? 'system',
                    'PRDO_FECHACAMBIO'  => now(),
                ]);

            $documento->setId($nuevoID);

            Cache::forget('documentos_proceso_' . $documento->getProceso()->getId());

            return true;
        } catch (Exception $e) {
            Log::error("Error al crear documento de proceso: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorID(int $id): ProcesoDocumento
    {
        $documento = new ProcesoDocumento(FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository());

        try {
            $registro = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->where('PRDO_ID', $id)
                ->first();

            if ($registro) {
                $procesoRepo = FabricaDeRepositorios::getInstance()->getProcesoRepository();
                $proceso = $procesoRepo->buscarProcesoPorId($registro->proc_id);

                $documento->setId($registro->prdo_id);
                $documento->setNombre($registro->prdo_nombre);
                $documento->setRuta($registro->prdo_ruta);
                $documento->setProceso($proceso);
            }
        } catch (Exception $e) {
            Log::error("Error al buscar documento de proceso por ID {$id}: " . $e->getMessage());
        }

        return $documento;
    }

    public function listarDocumentosPorProceso(int $procesoID): array
    {
        return Cache::remember('documentos_proceso_' . $procesoID, now()->addHours(4), function () use ($procesoID) {
            $documentos = [];

            try {
                $registros = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                    ->where('PROC_ID', $procesoID)
                    ->orderBy('PRDO_FECHACAMBIO', 'desc')
                    ->get();

                $procesoRepo = FabricaDeRepositorios::getInstance()->getProcesoRepository();
                $proceso = $procesoRepo->buscarProcesoPorId($procesoID);

                foreach ($registros as $registro) {
                    $documento = new ProcesoDocumento(FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository());
                    $documento->setId($registro->prdo_id);
                    $documento->setNombre($registro->prdo_nombre);
                    $documento->setRuta($registro->prdo_ruta);
                    $documento->setProceso($proceso);

                    $documentos[] = $documento;
                }
            } catch (Exception $e) {
                Log::error("Error al listar documentos del proceso ID {$procesoID}: " . $e->getMessage());
            }

            return $documentos;
        });
    }

    public function eliminar(int $documentoID): bool
    {
        try {
            $registro = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->where('PRDO_ID', $documentoID)
                ->first();

            if (!$registro) {
                return false;
            }

            $procesoID = $registro->proc_id;

            $eliminados = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_DOCUMENTO')
                ->where('PRDO_ID', $documentoID)
                ->delete();

            if ($eliminados > 0) {
                Cache::forget('documentos_proceso_' . $procesoID);
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error("Error al eliminar documento de proceso ID {$documentoID}: " . $e->getMessage());
            return false;
        }
    }
}
