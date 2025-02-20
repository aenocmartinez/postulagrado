<?php

namespace Src\admisiones\procesos\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Src\admisiones\procesos\domain\Proceso;
use Src\admisiones\procesos\repositories\ProcesoRepository;

class ProcesoDao extends Model implements ProcesoRepository 
{
    protected $table = 'procesos';
    protected $fillable = ['nombre', 'nivel_educativo', 'ruta_archivo_acto_administrativo', 'estado'];

    public static function listarProcesos(): array
    {
        $procesos = [];

        foreach(self::all() as $registro)
        {
            $proceso = new Proceso();
            $proceso->setId($registro->id);
            $proceso->setNombre($registro->nombre);
            $proceso->setNivelEducativo($registro->nivel_educativo);
            $proceso->setRutaArchivoActoAdministrativo($registro->ruta_archivo_acto_administrativo);
            $proceso->setEstado($registro->estado);

            $procesos[] = $proceso;
        }

        return $procesos;
    }

    public static function buscarProcesoPorId(int $id): Proceso 
    {
        $proceso = new Proceso();

        $registro = self::find($id);
        if ($registro) {
            $proceso->setId($registro->id);
            $proceso->setNombre($registro->nombre);
            $proceso->setNivelEducativo($registro->nivel_educativo);
            $proceso->setRutaArchivoActoAdministrativo($registro->ruta_archivo_acto_administrativo);
            $proceso->setEstado($registro->estado);
        }

        return $proceso;
    }
}