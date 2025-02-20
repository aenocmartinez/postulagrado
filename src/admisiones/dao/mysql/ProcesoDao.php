<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Proceso;
use Src\admisiones\repositories\ProcesoRepository;

class ProcesoDao extends Model implements ProcesoRepository
{
    protected $table = 'procesos';
    protected $fillable = ['nombre', 'nivel_educativo', 'ruta_archivo_acto_administrativo', 'estado'];

    public static function listarProcesos(): array
    {
        $procesos = [];
    
        try {

            $registros = self::all();

            foreach ($registros as $registro) {
                $proceso = new Proceso();
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);
                $proceso->setEstado($registro->estado);

                $rutaArchivo = "";
                if (!is_null($registro->ruta_archivo_acto_administrativo))
                {
                    $rutaArchivo = $registro->ruta_archivo_acto_administrativo;
                }
                $proceso->setRutaArchivoActoAdministrativo($rutaArchivo);
    
                $procesos[] = $proceso;
            }

        } catch (\Exception $e) {
            Log::error("Error en listarProcesos(): " . $e->getMessage());
        }
    
        return $procesos;
    }

    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, string $nivelEducativo): Proceso
    {
        $proceso = new Proceso();
    
        try {
            
            $registro = self::where('nombre', $nombre)
                            ->where('nivel_educativo', $nivelEducativo)
                            ->first();
    
            if ($registro) {
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);
                $proceso->setEstado($registro->estado);

                $proceso->setRutaArchivoActoAdministrativo($registro->ruta_archivo_acto_administrativo ?? "");
            }
    
        } catch (\Exception $e) {
            Log::error("Error en buscarProcesoPorNombreYNivelEducativo({$nombre}, {$nivelEducativo}): " . $e->getMessage());
        }
    
        return $proceso;
    }
    

    public static function buscarProcesoPorId(int $id): Proceso
    {
        $proceso = new Proceso();
    
        try {
            $registro = self::find($id);
    
            if ($registro) {
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);                
                $proceso->setEstado($registro->estado);

                $rutaArchivo = "";
                if (!is_null($registro->ruta_archivo_acto_administrativo))
                {
                    $rutaArchivo = $registro->ruta_archivo_acto_administrativo;
                }
                $proceso->setRutaArchivoActoAdministrativo($rutaArchivo);                
            }
        } catch (\Exception $e) {
            Log::error("Error en buscarProcesoPorId({$id}): " . $e->getMessage());
        }
    
        return $proceso;
    }
    

    public function crearProceso(Proceso $proceso): bool
    {
        try 
        {
            $registro = self::create([
                'nombre' => $proceso->getNombre(),
                'nivel_educativo' => $proceso->getNivelEducativo(),
                'estado' => $proceso->getEstado(),
            ]);
    
            if ($registro instanceof self) {
                $proceso->setId($registro->id);
                return true;
            }
    
        } catch (\Exception $e) {
            return false;
        }
    
        return false;
    }
    

    public function eliminarProceso(int $procesoID): bool
    {
        try {
            $eliminados = self::where('id', $procesoID)->delete();
    
            if ($eliminados === 0) {
                Log::warning("Intento de eliminar un proceso que no existe: ID {$procesoID}");
                return false;
            }
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al eliminar el proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarProceso(Proceso $proceso): bool
    {
        try {
            $filasAfectadas = self::where('id', $proceso->getId())
                ->update([
                    'nombre' => $proceso->getNombre(),
                    'nivel_educativo' => $proceso->getNivelEducativo(),
                    'ruta_archivo_acto_administrativo' => $proceso->getRutaArchivoActoAdministrativo(),
                    'estado' => $proceso->getEstado(),
                ]);
    
            if ($filasAfectadas === 0) {
                Log::warning("Intento de actualizar un proceso que no existe: ID {$proceso->getId()}");
                return false;
            }
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar el proceso ID {$proceso->getId()}: " . $e->getMessage());
            return false;
        }
    }

    public static function tieneCalendarioConActividades(int $procesoID): bool
    {
        try {
            return self::join('calendarios', 'procesos.id', '=', 'calendarios.proceso_id')
                ->join('actividades', 'calendarios.id', '=', 'actividades.calendario_id')
                ->where('procesos.id', $procesoID)
                ->exists();

        } catch (\Exception $e) {
            Log::error("Error al verificar si el proceso ID {$procesoID} tiene calendario con actividades: " . $e->getMessage());
            return false;
        }
    }    
        
}