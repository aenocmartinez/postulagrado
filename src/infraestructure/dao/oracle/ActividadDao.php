<?php

namespace Src\infraestructure\dao\oracle;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Src\domain\Actividad;
use Src\repositories\ActividadRepository;

class ActividadDao extends Model implements ActividadRepository
{
    protected $table = 'calendarios';
    protected $fillable = ['proceso_id'];    

    // public static function crearCalendario(Calendario $calendario): bool
    // {
    //     try {

    //         $registro = self::create([
    //             'proceso_id' => $calendario->getProceso()->getId(),
    //         ]);
    
    //         if ($registro instanceof self) {
    //             return true;
    //         }
    
    //     } catch (\Exception $e) {
    //         Log::error("Error al crear el calendario para el proceso ID {$calendario->getProceso()->getId()}: " . $e->getMessage());
    //         return false;
    //     }
    
    //     return false;
    // }
    
    public static function listarActividades(int $procesoID): array
    {
        return Cache::remember('actividades_listado_proceso_' . $procesoID, now()->addHours(4), function () use ($procesoID) {
            $actividades = [];
    
            try {
                $registros = DB::connection('oracle_academpostulgrado')
                            ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                            ->where('PROC_ID', $procesoID)
                            ->select('ACTI_ID', 'ACTI_DESCRIPCION', 'ACTI_FECHAINICIO', 'ACTI_FECHAFIN')
                            ->orderBy('ACTI_FECHAINICIO', 'asc')
                            ->get();
    
                foreach ($registros as $registro) {
                    $actividad = new Actividad();
                    $actividad->setId($registro->acti_id);
                    $actividad->setDescripcion($registro->acti_descripcion);
                    $actividad->setFechaInicio($registro->acti_fechainicio);
                    $actividad->setFechaFin($registro->acti_fechafin);
    
                    $actividades[] = $actividad;
                }
            } catch (\Exception $e) {
                Log::error("Error al listar actividades del proceso {$procesoID}: " . $e->getMessage());
            }
    
            return $actividades;
        });
    }          
    
    public static function agregarActividad(int $procesoID, Actividad $actividad): bool
    {
        try {
            $resultado = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->insert([
                    'ACTI_DESCRIPCION'   => $actividad->getDescripcion(),
                    'ACTI_FECHAINICIO'   => $actividad->getFechaInicio(),
                    'ACTI_FECHAFIN'      => $actividad->getFechaFin(),
                    'PROC_ID'            => $procesoID,
                    'ACTI_REGISTRADOPOR' => Auth::user()->id,
                    'ACTI_FECHACAMBIO'   => now(),
                ]);
    
            return $resultado;
        } catch (\Exception $e) {
            Log::error("Error al agregar actividad al proceso {$procesoID}: " . $e->getMessage());
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
        $actividad = new Actividad();
    
        try {
            $registro = DB::table('actividades')
                        ->select('id', 'descripcion', 'fecha_inicio', 'fecha_fin')
                        ->where('id', $actividadID)
                        ->first();
    
            if ($registro) {
                $actividad->setId($registro->id);
                $actividad->setDescripcion($registro->descripcion);
                $actividad->setFechaInicio($registro->fecha_inicio);
                $actividad->setFechaFin($registro->fecha_fin);
            }
        } catch (\Exception $e) {
            Log::error("Error al buscar actividad con ID {$actividadID}: " . $e->getMessage());
        }
    
        return $actividad;
    }
    
    public static function actualizarActividad(Actividad $actividad): bool
    {
        try {            
            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->where('ACTI_ID', $actividad->getId())
                ->update([
                    'ACTI_DESCRIPCION' => $actividad->getDescripcion(),
                    'ACTI_FECHAINICIO' => $actividad->getFechaInicio(),
                    'ACTI_FECHAFIN'    => $actividad->getFechaFin(),
                    'ACTI_FECHACAMBIO' => now(),
                ]);
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar la actividad ID {$actividad->getId()}: " . $e->getMessage());
            return false;
        }
    }    

}