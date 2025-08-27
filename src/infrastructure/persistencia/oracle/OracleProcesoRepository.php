<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\NivelEducativo;
use Src\domain\Notificacion;
use Src\domain\proceso\Proceso;
use Src\domain\ProgramaProceso;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\di\FabricaDeRepositoriosOracle;

class OracleProcesoRepository extends Model implements ProcesoRepository
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

                foreach ($registros as $registro) {
                    $proceso = new Proceso();

                    $proceso->setId($registro->proc_id);
                    $proceso->setNombre($registro->proc_nombre);
                    $proceso->setNivelEducativoID($registro->nied_id);
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
        $proceso = new Proceso();
    
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
                $proceso->setNivelEducativoID($nivelEducativo->getId());
                $proceso->setEstado($registro->proc_estado);                
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
        $cacheKey = "proceso_{$id}";

        return Cache::remember($cacheKey, now()->addHours(4), function () use ($id) {
            $proceso = new Proceso();

            try {
                $r = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROCESO AS P')
                    ->leftJoin('ACADEMICO.NIVELEDUCATIVO AS NIED', 'NIED.NIED_ID', '=', 'P.NIED_ID')
                    ->select(
                        'P.PROC_ID            AS proc_id',
                        'P.PROC_NOMBRE        AS proc_nombre',
                        'P.NIED_ID            AS nied_id',
                        'P.PROC_ESTADO        AS proc_estado',
                        'NIED.NIED_DESCRIPCION AS nied_nombre'
                    )
                    ->where('P.PROC_ID', $id)
                    ->first();

                if (!$r) {
                    return $proceso; 
                }

                $proceso->setId((int)($r->proc_id ?? 0));
                $proceso->setNombre(trim((string)($r->proc_nombre ?? '')));
                $proceso->setNivelEducativoID((int)($r->nied_id ?? 0));
                $proceso->setEstado((string)($r->proc_estado ?? ''));
                $proceso->setNivelEducativoNombre(trim((string)($r->nied_nombre ?? '')));
            } catch (\Throwable $e) {
                Log::error("Error en buscarProcesoPorId({$id}): " . $e->getMessage(), ['exception' => $e]);
            }

            return $proceso;
        });
    }
    
    public function crearProceso(Proceso $proceso): bool
    {
        try 
        {
            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_PROCESO_ID.NEXTVAL AS id FROM DUAL')
                ->id;

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->insert([
                    'PROC_ID'            => $nuevoID,
                    'PROC_NOMBRE'        => $proceso->getNombre(),
                    'NIED_ID'            => $proceso->getNivelEducativoID(),
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
                    'NIED_ID'     => $proceso->getNivelEducativoID(),
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

    // public function listarProgramas(int $procesoID): array
    // {
    //     return Cache::remember('programas_proceso_' . $procesoID, now()->addHours(4), function () use ($procesoID) {
    //         $programasProceso = [];
    
    //         try {
    //             $procesoProgramas = DB::connection('oracle_academpostulgrado')
    //                 ->table('ACADEMPOSTULGRADO.PROCESO_PROGRAMA')
    //                 ->where('PROC_ID', $procesoID)
    //                 ->select('PROGR_ID AS proceso_programa_id', 'PROG_ID AS programa_id')
    //                 ->get()
    //                 ->keyBy('programa_id');
    
    //             if ($procesoProgramas->isEmpty()) {
    //                 return $programasProceso;
    //             }
    
    //             $programaRepository = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
    
    //             foreach ($procesoProgramas as $programaID => $procesoPrograma) {
    //                 $programa = $programaRepository->buscarPorId($programaID);
    
    //                 if (!$programa || !$programa->existe()) {
    //                     continue;
    //                 }
    
    //                 $programaProceso = new ProgramaProceso();
    //                 $programaProceso->setId($procesoPrograma->proceso_programa_id);
    //                 $programaProceso->setPrograma($programa);
    
    //                 $programasProceso[] = $programaProceso;
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Error al listar programas del proceso ID {$procesoID}: " . $e->getMessage());
    //         }
    
    //         return $programasProceso;
    //     });
    // }

    public function buscarProgramaPorProceso(int $procesoID, int $programaID): ProgramaProceso
    {
        // return Cache::remember('programa_proceso_' . $procesoID . '_' . $programaID, now()->addHours(4), function () use ($procesoID, $programaID) {
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
    
                $programa = FabricaDeRepositoriosOracle::getInstance()
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
        // });
    }

    public function agregarCandidatoAProceso(int $programaProcesoID, int $codigoEstudiante, int $anio, int $periodo): bool 
    {
        try {
            
            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_PPROG_ESTUD_ID.NEXTVAL AS id FROM DUAL')->id;

            
            $usuario = Auth::check() ? Auth::user()->id : 'sistema';

            DB::connection('oracle_academpostulgrado')->table('PROCESO_PROGRAMA_ESTUDIANTES')->insert([
                'PPES_ID'            => $nuevoID,
                'PROGR_ID'           => $programaProcesoID,
                'ESTU_CODIGO'        => (string) $codigoEstudiante,
                'PPES_REGISTRADOPOR' => $usuario,
                'PPES_FECHACAMBIO'   => now(),
                'PPES_ANO'           => $anio,
                'PPES_PERIODO'       => $periodo,
            ]);

            return true;
        } catch (\Throwable $e) {
            logger()->error('Error en agregarCandidatoAProceso: ' . $e->getMessage());
            return false;
        }
    }

    public function listarCandidatosPorProcesoYPrograma(int $procesoId, int $programaID): array
    {
        $sql = "
            SELECT 
                ppes.PPES_ID,
                ppes.ESTU_CODIGO
            FROM ACADEMPOSTULGRADO.PROCESO_PROGRAMA_ESTUDIANTES ppes
            INNER JOIN ACADEMPOSTULGRADO.PROCESO_PROGRAMA pp
                ON pp.PROGR_ID = ppes.PROGR_ID
            WHERE pp.PROC_ID = :proceso_id
            AND pp.PROG_ID = :programa_id
        ";

        return DB::connection('oracle_academpostulgrado')
            ->select($sql, [
                'proceso_id' => $procesoId,
                'programa_id' => $programaID,
            ]);
    }


}