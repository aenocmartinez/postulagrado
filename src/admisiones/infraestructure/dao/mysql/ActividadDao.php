<?php

namespace Src\admisiones\infraestructure\dao\mysql;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Src\admisiones\domain\Actividad;
use Src\admisiones\domain\Calendario;
use Src\admisiones\repositories\ActividadRepository;

class ActividadDao extends Model implements ActividadRepository
{
    protected $table = 'calendarios';
    protected $fillable = ['proceso_id'];    

    public static function crearCalendario(Calendario $calendario): bool
    {
        try {

            $registro = self::create([
                'proceso_id' => $calendario->getProceso()->getId(),
            ]);
    
            if ($registro instanceof self) {
                return true;
            }
    
        } catch (\Exception $e) {
            Log::error("Error al crear el calendario para el proceso ID {$calendario->getProceso()->getId()}: " . $e->getMessage());
            return false;
        }
    
        return false;
    }
    
    public static function listarActividades(int $procesoID): array
    {
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
    }
       
    
    public static function agregarActividad(int $procesoID, Actividad $actividad): bool
    {
        try {
            $calendario = self::where('proceso_id', $procesoID)->first();
    
            if (!$calendario) {
                Log::warning("No se pudo agregar la actividad: El proceso {$procesoID} no tiene un calendario.");
                return false;
            }
    
            $resultado = DB::table('actividades')->insert([
                'calendario_id' => $calendario->id,
                'descripcion' => $actividad->getDescripcion(),
                'fecha_inicio' => $actividad->getFechaInicio(),
                'fecha_fin' => $actividad->getFechaFin(),
                'created_at' => now(),
                'updated_at' => now()
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
            $deleted = DB::table('actividades')->where('id', $actividadID)->delete();
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
            DB::table('actividades')
                ->where('id', $actividad->getId())
                ->update([
                    'descripcion'  => $actividad->getDescripcion(),
                    'fecha_inicio' => $actividad->getFechaInicio(),
                    'fecha_fin'    => $actividad->getFechaFin(),
                    'updated_at'   => now(),
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar la actividad ID {$actividad->getId()}: " . $e->getMessage());
            return false;
        }
    }

}