<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
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
    
       
}