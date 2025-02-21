<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Src\admisiones\domain\Actividad;
use Src\admisiones\domain\Calendario;
use Src\admisiones\domain\Proceso;
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
       
}