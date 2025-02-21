<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Src\admisiones\domain\Actividad;
use Src\admisiones\domain\Calendario;
use Src\admisiones\repositories\CalendarioRepository;

class CalendarioDao extends Model implements CalendarioRepository
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
            $registros = self::join('actividades', 'calendarios.id', '=', 'actividades.calendario_id')
                            ->where('calendarios.proceso_id', $procesoID)
                            ->select('actividades.id', 'actividades.descripcion', 'actividades.fecha_inicio', 'actividades.fecha_fin')
                            ->orderBy('actividades.fecha_inicio', 'asc')
                            ->get();
    
            foreach ($registros as $registro) {
                $actividad = new Actividad();
                $actividad->setId($registro->id);
                $actividad->setDescripcion($registro->descripcion);
                $actividad->setFechaInicio($registro->fecha_inicio);
                $actividad->setFechaFin($registro->fecha_fin);
    
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
    
}