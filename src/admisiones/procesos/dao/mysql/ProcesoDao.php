<?php

namespace Src\admisiones\procesos\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Src\admisiones\procesos\domain\Proceso;
use Src\admisiones\procesos\repositories\ProcesoRepository;

use Illuminate\Support\Facades\Log;

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
    

    public static function buscarProcesoPorNombre(string $nombre): Proceso
    {
        $proceso = new Proceso();
    
        try {
            $registro = self::where('nombre', $nombre)->first();
    
            if ($registro) {
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);
                $proceso->setRutaArchivoActoAdministrativo($registro->ruta_archivo_acto_administrativo);
                $proceso->setEstado($registro->estado);
            }

        } catch (\Exception $e) {
            Log::error('Error en buscarProcesoPorNombre: ' . $e->getMessage());
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
                $proceso->setRutaArchivoActoAdministrativo($registro->ruta_archivo_acto_administrativo);
                $proceso->setEstado($registro->estado);
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
    
        } catch (\Exception $e) {
            return false;
        }

        return $registro instanceof self;
    }
    
}