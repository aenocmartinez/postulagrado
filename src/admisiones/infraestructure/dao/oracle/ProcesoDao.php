<?php

namespace Src\admisiones\infraestructure\dao\oracle;

use Exception;
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
        $procesos = [];
    
        try 
        {
            $registros = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->select('PROC_ID', 'PROC_NOMBRE', 'NIED_ID', 'PROC_ESTADO')
                ->orderBy('PROC_ID')
                ->get();
    
            $procesoRepo = FabricaDeRepositorios::getInstance()->getProcesoRepository();
            $calendarioRepo = FabricaDeRepositorios::getInstance()->getCalendarioRepository();
            $nivelEducativoRepo = FabricaDeRepositorios::getInstance()->getNivelEducativoRepository();
    
            foreach ($registros as $registro) {
                $proceso = new Proceso($procesoRepo, $calendarioRepo, $nivelEducativoRepo);
    
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
    }    

    public static function buscarProcesoPorNombreYNivelEducativo(string $nombre, NivelEducativo $nivelEducativo): Proceso
    {
        $proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository(),
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
        $proceso = new Proceso(
            FabricaDeRepositorios::getInstance()->getProcesoRepository(),
            FabricaDeRepositorios::getInstance()->getCalendarioRepository(),
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
        );
    
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
    }    
    
    public function crearProceso(Proceso $proceso): bool
    {
        try {
            $nivelEducativoID = $proceso->getNivelEducativo()->getId();
    
            $insertado = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROCESO')
                ->insert([
                    'PROC_NOMBRE'        => $proceso->getNombre(),
                    'NIED_ID'            => $nivelEducativoID,
                    'PROC_ESTADO'        => $proceso->getEstado(),
                    'PROC_REGISTRADOPOR' => 'ABIMELEC',
                ]);
    
            return $insertado;
        } catch (Exception $e) {
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
    
            return true;
        } 
        catch (Exception $e) 
        {
            Log::error("Error en actualizarProceso(): " . $e->getMessage());
            return false;
        }
    }    

    // Están pendientes de migrar
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
    
        // try {            

        //     $procesoProgramas = DB::table('proceso_programa')
        //         ->where('proceso_id', $procesoID)
        //         ->select('id as proceso_programa_id', 'programa_id')
        //         ->get()
        //         ->keyBy('programa_id');
    
        //     if ($procesoProgramas->isEmpty()) {
        //         return $programasProceso;
        //     }

        //     $programaIds = $procesoProgramas->keys()->toArray();

        //     $programasDao = ProgramaDao::whereIn('id', $programaIds)->get();
    
        //     foreach ($programasDao as $programaDao) {
        //         $programa = new Programa(
        //             FabricaDeRepositorios::getInstance()->getProgramaRepository()
        //         );

        //         $programa->setId($programaDao->id);
        //         $programa->setNombre($programaDao->nombre);
        //         $programa->setCodigo($programaDao->codigo);
        //         $programa->setSnies($programaDao->snies);
        //         $programa->setMetodologia($programaDao->metodologia());
        //         $programa->setNivelEducativo($programaDao->nivelEducativo());
        //         $programa->setModalidad($programaDao->modalidad());
        //         $programa->setUnidadRegional($programaDao->unidadRegional());
        //         $programa->setJornada($programaDao->jornada());

        //         $proceso_programa_id = $procesoProgramas[$programaDao->id]->proceso_programa_id ?? null;

        //         $programaProceso = new ProgramaProceso();
        //         $programaProceso->setId($proceso_programa_id);
        //         $programaProceso->setPrograma($programa);

        //         $programasProceso[] = $programaProceso;
        //     }
        // } catch (Exception $e) {
        //     Log::error("Error al listar programas del proceso ID {$procesoID}: " . $e->getMessage());
        // }
    
        return $programasProceso;
    }

    public function buscarProgramaPorProceso(int $procesoID, int $programaID): ProgramaProceso
    {
        $programaProceso = new ProgramaProceso();
        // try {
        //     $procesoPrograma = DB::table('proceso_programa')
        //         ->where('proceso_id', $procesoID)
        //         ->where('programa_id', $programaID)
        //         ->select('id as proceso_programa_id', 'programa_id')
        //         ->first();

        //     if (!$procesoPrograma) {
        //         return $programaProceso;
        //     }

        //     $programaDao = ProgramaDao::where('id', $programaID)->first();

        //     if (!$programaDao) {
        //         return $programaProceso;
        //     }

        //     $programa = new Programa(
        //         FabricaDeRepositorios::getInstance()->getProgramaRepository()
        //     );

        //     $programa->setId($programaDao->id);
        //     $programa->setNombre($programaDao->nombre);
        //     $programa->setCodigo($programaDao->codigo);
        //     $programa->setSnies($programaDao->snies);
        //     $programa->setMetodologia($programaDao->metodologia());
        //     $programa->setNivelEducativo($programaDao->nivelEducativo());
        //     $programa->setModalidad($programaDao->modalidad());
        //     $programa->setUnidadRegional($programaDao->unidadRegional());
        //     $programa->setJornada($programaDao->jornada());

            
        //     $programaProceso->setId($procesoPrograma->proceso_programa_id);
        //     $programaProceso->setPrograma($programa);

        // } catch (Exception $e) {
        //     Log::error("Error al buscar el programa ID {$programaID} en el proceso ID {$procesoID}: " . $e->getMessage());
        //     return $programaProceso;
        // }
        
        return $programaProceso;
    }

    
}