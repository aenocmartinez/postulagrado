<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Src\domain\proceso\actividad\Actividad;
use Src\domain\proceso\actividad\valueObject\ActividadId;
use Src\domain\proceso\actividad\valueObject\DescripcionActividad;
use Src\domain\proceso\actividad\valueObject\RangoFechasActividad;
use Src\domain\repositories\ActividadRepository;

class OracleActividadRepository extends Model implements ActividadRepository
{
    protected $table = 'calendarios';
    protected $fillable = ['proceso_id'];    
    
    public static function listarActividades(int $procesoID): array
    {
        $rows = Cache::remember("actividades_listado_proceso_{$procesoID}", now()->addHours(4), function () use ($procesoID) {
            return DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->where('PROC_ID', $procesoID)
                ->select('ACTI_ID','ACTI_DESCRIPCION','ACTI_FECHAINICIO','ACTI_FECHAFIN')
                ->orderBy('ACTI_FECHAINICIO','asc')
                ->get()
                ->map(fn($r) => [
                    'id'          => (int) $r->acti_id,
                    'descripcion' => (string) $r->acti_descripcion,
                    'inicio'      => (string) $r->acti_fechainicio,
                    'fin'         => (string) $r->acti_fechafin,
                ])->all();
        });

        
        $actividades = [];
        foreach ($rows as $r) {
            $actividades[] = new Actividad(
                ActividadId::fromInt($r['id']),
                new DescripcionActividad($r['descripcion']),
                new RangoFechasActividad($r['inicio'], $r['fin'])
            );
        }

        return $actividades;
    }       
    
    public static function agregarActividad(int $procesoID, Actividad $actividad): bool
    {    
        try {
            $ok = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->insert([
                    'ACTI_DESCRIPCION'   => $actividad->descripcion()->value(),
                    'ACTI_FECHAINICIO'   => $actividad->inicio()->format('Y-m-d H:i:s'),
                    'ACTI_FECHAFIN'      => $actividad->fin()->format('Y-m-d H:i:s'),
                    'PROC_ID'            => $procesoID,
                    'ACTI_REGISTRADOPOR' => Auth::id(),
                    'ACTI_FECHACAMBIO'   => now(),
                ]);

            Cache::forget("actividades_listado_proceso_{$procesoID}");

            return (bool) $ok;
        } catch (\Throwable $e) {
            Log::error("Error al agregar actividad al proceso {$procesoID}: {$e->getMessage()}");
            return false;
        }
    }
    
    public static function eliminarActividad(int $actividadID): bool
    {
        try {
            $deleted = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->where('ACTI_ID', $actividadID)
                ->delete();
    
            return $deleted > 0;
        } catch (\Exception $e) {
            Log::error("Error al eliminar actividad con ID {$actividadID}: " . $e->getMessage());
            return false;
        }
    }    
    
    public static function buscarActividadPorId(int $actividadID): Actividad
    {
        try {
            $r = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->select('ACTI_ID','ACTI_DESCRIPCION','ACTI_FECHAINICIO','ACTI_FECHAFIN')
                ->where('ACTI_ID', $actividadID)
                ->first();

            if (!$r) {
                return Actividad::vacia(); 
            }

            return new Actividad(
                ActividadId::fromInt((int) $r->acti_id),
                new DescripcionActividad((string) $r->acti_descripcion),
                new RangoFechasActividad((string) $r->acti_fechainicio, (string) $r->acti_fechafin)
            );
        } catch (\Throwable $e) {
            return Actividad::vacia(); 
        }
    }
    
    public static function actualizarActividad(Actividad $actividad): bool
    {
        try {
            $id = $actividad->id()->value();
            if ($id === null) {
                Log::warning('No se puede actualizar una actividad sin ID.');
                return false;
            }

            $afectadas = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->where('ACTI_ID', $id)
                ->update([
                    'ACTI_DESCRIPCION' => $actividad->descripcion()->value(),
                    'ACTI_FECHAINICIO' => $actividad->inicio()->format('Y-m-d H:i:s'),
                    'ACTI_FECHAFIN'    => $actividad->fin()->format('Y-m-d H:i:s'),
                    'ACTI_FECHACAMBIO' => now(),
                ]);

            return $afectadas > 0;
        } catch (\Throwable $e) {
            Log::error("Error al actualizar la actividad ID {$id}: {$e->getMessage()}");
            return false;
        }
    }
  

}