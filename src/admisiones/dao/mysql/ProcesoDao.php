<?php

namespace Src\admisiones\dao\mysql;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Proceso;
use Src\admisiones\domain\Programa;
use Src\admisiones\domain\ProgramaProceso;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProcesoDao extends Model implements ProcesoRepository
{
    protected $table = 'procesos';
    protected $fillable = ['nombre', 'nivel_educativo', 'estado'];

    public static function listarProcesos(): array
    {
        $procesos = [];
    
        try {

            $registros = self::all();

            foreach ($registros as $registro) {
                $proceso = new Proceso(
                    FabricaDeRepositorios::getInstance()->getProcesoRepository(),
                    FabricaDeRepositorios::getInstance()->getCalendarioRepository()
                );
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);
                $proceso->setEstado($registro->estado);
    
                $procesos[] = $proceso;
            }

        } catch (Exception $e) {
            Log::error("Error en listarProcesos(): " . $e->getMessage());
        }
    
        return $procesos;
    }

    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, string $nivelEducativo): Proceso
    {
        $proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository(),
        );
    
        try {
            
            $registro = self::where('nombre', $nombre)
                            ->where('nivel_educativo', $nivelEducativo)
                            ->first();
    
            if ($registro) {
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);
                $proceso->setEstado($registro->estado);
            }
    
        } catch (Exception $e) {
            Log::error("Error en buscarProcesoPorNombreYNivelEducativo({$nombre}, {$nivelEducativo}): " . $e->getMessage());
        }
    
        return $proceso;
    }
    

    public static function buscarProcesoPorId(int $id): Proceso
    {
        $proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository()
        );
    
        try {
            $registro = self::find($id);
    
            if ($registro) {
                $proceso->setId($registro->id);
                $proceso->setNombre($registro->nombre);
                $proceso->setNivelEducativo($registro->nivel_educativo);                
                $proceso->setEstado($registro->estado);
            
            }
        } catch (Exception $e) {
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
    
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
                    'estado' => $proceso->getEstado(),
                ]);
    
            if ($filasAfectadas === 0) {
                Log::warning("Intento de actualizar un proceso que no existe: ID {$proceso->getId()}");
                return false;
            }
    
            return true;
        } catch (Exception $e) {
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

        } catch (Exception $e) {
            Log::error("Error al verificar si el proceso ID {$procesoID} tiene calendario con actividades: " . $e->getMessage());
            return false;
        }
    }   
    
    public function agregarPrograma(int $procesoID, int $programaID): bool {
        try {

            $existe = DB::table('proceso_programa')
                ->where('proceso_id', $procesoID)
                ->where('programa_id', $programaID)
                ->exists();
    
            if ($existe) {
                Log::warning("El programa ID {$programaID} ya está asociado al proceso ID {$procesoID}");
                return false;
            }
    
            DB::table('proceso_programa')->insert([
                'proceso_id' => $procesoID,
                'programa_id' => $programaID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            return true;
        } catch (Exception $e) {
            Log::error("Error al agregar programa ID {$programaID} al proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }
    
    public function quitarPrograma(int $procesoID, int $programaID): bool {
        try {
            $existe = DB::table('proceso_programa')
                ->where('proceso_id', $procesoID)
                ->where('programa_id', $programaID)
                ->exists();
    
            if (!$existe) {
                Log::warning("El programa ID {$programaID} no está asociado al proceso ID {$procesoID}");
                return false;
            }

            DB::table('proceso_programa')
                ->where('proceso_id', $procesoID)
                ->where('programa_id', $programaID)
                ->delete();
    
            return true;
        } catch (Exception $e) {
            Log::error("Error al quitar programa ID {$programaID} del proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }  
    
    public function quitarTodosLosPrograma(int $procesoID): bool {
        try {            
            DB::table('proceso_programa')->where('proceso_id', $procesoID)->delete();
            return true;
        } catch (Exception $e) {
            Log::error("Error al quitar todos los programa del proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }

    public function listarProgramas(int $procesoID): array {
        $programasProceso = [];
    
        try {            

            $procesoProgramas = DB::table('proceso_programa')
                ->where('proceso_id', $procesoID)
                ->select('id as proceso_programa_id', 'programa_id')
                ->get()
                ->keyBy('programa_id');
    
            if ($procesoProgramas->isEmpty()) {
                return $programasProceso;
            }

            $programaIds = $procesoProgramas->keys()->toArray();

            $programasDao = ProgramaDao::whereIn('id', $programaIds)->get();
    
            foreach ($programasDao as $programaDao) {
                $programa = new Programa(
                    FabricaDeRepositorios::getInstance()->getProgramaRepository()
                );

                $programa->setId($programaDao->id);
                $programa->setNombre($programaDao->nombre);
                $programa->setCodigo($programaDao->codigo);
                $programa->setSnies($programaDao->snies);
                $programa->setMetodologia($programaDao->metodologia());
                $programa->setNivelEducativo($programaDao->nivelEducativo());
                $programa->setModalidad($programaDao->modalidad());
                $programa->setUnidadRegional($programaDao->unidadRegional());
                $programa->setJornada($programaDao->jornada());

                $proceso_programa_id = $procesoProgramas[$programaDao->id]->proceso_programa_id ?? null;

                $programaProceso = new ProgramaProceso();
                $programaProceso->setId($proceso_programa_id);
                $programaProceso->setPrograma($programa);

                $programasProceso[] = $programaProceso;
            }
        } catch (Exception $e) {
            Log::error("Error al listar programas del proceso ID {$procesoID}: " . $e->getMessage());
        }
    
        return $programasProceso;
    }
    
}