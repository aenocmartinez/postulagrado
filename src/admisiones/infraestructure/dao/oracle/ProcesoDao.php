<?php

namespace Src\admisiones\infraestructure\dao\oracle;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\Proceso;
use Src\admisiones\domain\Programa;
use Src\admisiones\domain\ProgramaProceso;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProcesoDao extends Model implements ProcesoRepository
{
    protected $connection = 'oracle_academpostulgrado';
    protected $table = 'ACADEMPOSTULGRADO.PROCESO';
    protected $primaryKey = 'PROC_ID';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['PROC_NOMBRE', 'NIED_ID', 'PROC_ESTADO'];

    public static function listarProcesos(): array
    {
        return Cache::remember('procesos_listado', now()->addHour(8), function () {
            $procesos = [];

            try 
            {
                $registros = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO')
                    ->select('PROC_ID', 'PROC_NOMBRE', 'NIED_ID', 'PROC_ESTADO')
                    ->orderBy('PROC_ID')
                    ->get();

                $procesoRepo = FabricaDeRepositorios::getInstance()->getProcesoRepository();
                $calendarioRepo = FabricaDeRepositorios::getInstance()->getActividadRepository();
                $nivelEducativoRepo = FabricaDeRepositorios::getInstance()->getNivelEducativoRepository();

                foreach ($registros as $registro) {
                    $proceso = new Proceso($procesoRepo, $calendarioRepo, $nivelEducativoRepo);
                    $proceso->setDocumentoRepo(FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository());

                    $nivelEducativo = $nivelEducativoRepo->BuscarPorID($registro->nied_id);

                    $proceso->setId($registro->proc_id);
                    $proceso->setNombre($registro->proc_nombre);
                    $proceso->setNivelEducativo($nivelEducativo);
                    $proceso->setEstado($registro->proc_estado);

                    $procesos[] = $proceso;
                }
            } 
            catch (Exception $e) 
            {
                Log::error("Error en listarProcesos(): " . $e->getMessage());
            }

            return $procesos;
        });
    }  

    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, NivelEducativo $nivelEducativo): Proceso
    {
        $proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getActividadRepository(),
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
        );
    
        try 
        {
            $registro = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->select('PROC_ID', 'PROC_NOMBRE', 'NIED_ID', 'PROC_ESTADO')
                ->where('PROC_NOMBRE', $nombre)
                ->where('NIED_ID', $nivelEducativo->getId())
                ->first();
    
            if ($registro) {
                $proceso->setId($registro->proc_id);
                $proceso->setNombre($registro->proc_nombre);
                $proceso->setNivelEducativo($nivelEducativo);
                $proceso->setEstado($registro->proc_estado);
                $proceso->setDocumentoRepo(FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository());
            }
        } 
        catch (Exception $e) 
        {
            Log::error("Error en buscarProcesoPorNombreYNivelEducativo({$nombre}, {$nivelEducativo->getId()}): " . $e->getMessage());
        }
    
        return $proceso;
    }    

    public static function buscarProcesoPorId(int $id): Proceso
    {
        return Cache::remember('proceso_' . $id, now()->addHours(4), function () use ($id) {
            $proceso = new Proceso(
                FabricaDeRepositorios::getInstance()->getProcesoRepository(),
                FabricaDeRepositorios::getInstance()->getActividadRepository(),
                FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
            );

            $proceso->setDocumentoRepo(FabricaDeRepositorios::getInstance()->getProcesoDocumentoRepository());
    
            try 
            {
                $registro = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO')
                    ->select('PROC_ID', 'PROC_NOMBRE', 'NIED_ID', 'PROC_ESTADO')
                    ->where('PROC_ID', $id)
                    ->first();
    
                if ($registro) {
                    $nivelEducativoRepo = FabricaDeRepositorios::getInstance()->getNivelEducativoRepository();
                    $nivelEducativo = $nivelEducativoRepo->BuscarPorID($registro->nied_id);
    
                    $proceso->setId($registro->proc_id);
                    $proceso->setNombre($registro->proc_nombre);
                    $proceso->setNivelEducativo($nivelEducativo);
                    $proceso->setEstado($registro->proc_estado);
                }
            } 
            catch (Exception $e) 
            {
                Log::error("Error en buscarProcesoPorId({$id}): " . $e->getMessage());
            }
    
            return $proceso;
        });
    }
    
    
    public function crearProceso(Proceso $proceso): bool
    {
        try {
            $nivelEducativoID = $proceso->getNivelEducativo()->getId();

            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_PROCESO_ID.NEXTVAL AS id FROM DUAL')
                ->id;

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->insert([
                    'PROC_ID'            => $nuevoID,
                    'PROC_NOMBRE'        => $proceso->getNombre(),
                    'NIED_ID'            => $nivelEducativoID,
                    'PROC_ESTADO'        => $proceso->getEstado(),
                    'PROC_REGISTRADOPOR' => Auth::user()->id,
                    'PROC_FECHACAMBIO'   => now(),
                ]);

            $proceso->setId($nuevoID);

            Cache::forget('procesos_listado');
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error en crearProceso(): " . $e->getMessage());
            return false;
        }
    }    
    
    public function eliminarProceso(int $procesoID): bool
    {
        try 
        {
            $eliminados = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->where('PROC_ID', $procesoID)
                ->delete();

                Cache::forget('procesos_listado');

            return $eliminados > 0;
        } 
        catch (Exception $e) 
        {
            Log::error("Error en eliminarProceso({$procesoID}): " . $e->getMessage());
            return false;
        }
    }

    public function actualizarProceso(Proceso $proceso): bool
    {
        try 
        {
            $filas = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->where('PROC_ID', $proceso->getId())
                ->update([
                    'PROC_NOMBRE' => $proceso->getNombre(),
                    'NIED_ID'     => $proceso->getNivelEducativo()->getId(),
                    'PROC_ESTADO' => $proceso->getEstado(),
                ]);
    
            if ($filas === 0) {
                Log::warning("Intento de actualizar un proceso inexistente: {$proceso->getId()}");
                return false;
            }
            
            Cache::forget('procesos_listado');

            return true;
        } 
        catch (Exception $e) 
        {
            Log::error("Error en actualizarProceso(): " . $e->getMessage());
            return false;
        }
    }    

    // Están pendientes de migrar
    public static function tieneActividades(int $procesoID): bool
    {
        try {
            return DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.ACTIVIDAD')
                ->where('PROC_ID', $procesoID)
                ->exists();
    
        } catch (\Exception $e) {
            Log::error("Error al verificar si el proceso ID {$procesoID} tiene actividades: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function agregarPrograma(int $procesoID, int $programaID): bool
    {
        try {
    
            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
                ->insert([
                    'PROC_ID'            => $procesoID,
                    'PROG_ID'            => $programaID,
                    'PROGR_REGISTRADOPOR' => Auth::user()->id ?? 'system', // O usuario del sistema si aplica
                    'PROGR_FECHACAMBIO'   => now(),
                ]);
    
            return true;

        } catch (\Exception $e) {
            Log::error("Error al agregar programa ID {$programaID} al proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }
    
    public function quitarPrograma(int $procesoID, int $programaID): bool
    {
        try {
            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
                ->where('PROC_ID', $procesoID)
                ->where('PROG_ID', $programaID)
                ->delete();
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al quitar programa ID {$programaID} del proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }
    
    public function quitarTodosLosPrograma(int $procesoID): bool
    {
        try {            
            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
                ->where('PROC_ID', $procesoID)
                ->delete();
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al quitar todos los programas del proceso ID {$procesoID}: " . $e->getMessage());
            return false;
        }
    }    

    public function listarProgramas(int $procesoID): array
    {
        return Cache::remember('programas_proceso_' . $procesoID, now()->addHours(4), function () use ($procesoID) {
            $programasProceso = [];
    
            try {
                $procesoProgramas = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
                    ->where('PROC_ID', $procesoID)
                    ->select('PROGR_ID AS proceso_programa_id', 'PROG_ID AS programa_id')
                    ->get()
                    ->keyBy('programa_id');
    
                if ($procesoProgramas->isEmpty()) {
                    return $programasProceso;
                }
    
                $programaRepository = FabricaDeRepositorios::getInstance()->getProgramaRepository();
    
                foreach ($procesoProgramas as $programaID => $procesoPrograma) {
                    $programa = $programaRepository->buscarPorId($programaID);
    
                    if (!$programa || !$programa->existe()) {
                        continue;
                    }
    
                    $programaProceso = new ProgramaProceso();
                    $programaProceso->setId($procesoPrograma->proceso_programa_id);
                    $programaProceso->setPrograma($programa);
    
                    $programasProceso[] = $programaProceso;
                }
            } catch (\Exception $e) {
                Log::error("Error al listar programas del proceso ID {$procesoID}: " . $e->getMessage());
            }
    
            return $programasProceso;
        });
    }

    public function buscarProgramaPorProceso(int $procesoID, int $programaID): ProgramaProceso
    {
        return Cache::remember('programa_proceso_' . $procesoID . '_' . $programaID, now()->addHours(4), function () use ($procesoID, $programaID) {
            $programaProceso = new ProgramaProceso();
    
            try {
                $procesoPrograma = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
                    ->where('PROC_ID', $procesoID)
                    ->where('PROG_ID', $programaID)
                    ->select('PROGR_ID AS proceso_programa_id', 'PROG_ID AS programa_id')
                    ->first();
    
                if (!$procesoPrograma) {
                    return $programaProceso;
                }
    
                $programa = FabricaDeRepositorios::getInstance()
                    ->getProgramaRepository()
                    ->buscarPorId($programaID);
    
                if (!$programa || !$programa->existe()) {
                    return $programaProceso;
                }
    
                $programaProceso->setId($procesoPrograma->proceso_programa_id);
                $programaProceso->setPrograma($programa);
    
            } catch (\Exception $e) {
                Log::error("Error al buscar el programa ID {$programaID} en el proceso ID {$procesoID}: " . $e->getMessage());
                return $programaProceso;
            }
    
            return $programaProceso;
        });
    }
      
    
}