<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\ProgramaContacto;
use Src\domain\repositories\ProgramaContactoRepository;
use Src\shared\di\FabricaDeRepositoriosOracle;

class OracleProgramaContactoRepository extends Model implements ProgramaContactoRepository {

    protected $connection = 'oracle_academpostulgrado';

    protected $table = 'ACADEMPOSTULGRADO.PROGRAMA_CONTACTOS';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'PRCO_ID';

    protected $fillable = ['PRCO_NOMBRE', 'PRCO_TELEFONO', 'PRCO_CORREO', 'PRCO_OBSERVACION', 'PROG_ID'];

    /**
     * Listar los contactos filtrados por un criterio de búsqueda opcional.
     */

     public function listar(string $criterio = ""): array {
        $contactos = [];
        $cacheKey = 'contactos_lista_cache'; // Usamos una clave de caché más fácil de identificar
        
        try {
            // Verificamos si los resultados ya están en caché
            $contactos = Cache::remember($cacheKey, now()->addHours(6), function () use ($criterio) {
                $query = self::query()
                    ->select(
                        'programa_contactos.PRCO_ID',
                        'programa_contactos.PRCO_NOMBRE',
                        'programa_contactos.PRCO_TELEFONO',
                        'programa_contactos.PRCO_CORREO',
                        'programa_contactos.PRCO_OBSERVACION',
                        'programa_contactos.PROG_ID'
                    );
        
                if (!empty($criterio)) {
                    $query->where(function ($q) use ($criterio) {
                        $q->where('programa_contactos.PRCO_NOMBRE', 'LIKE', "%{$criterio}%")
                          ->orWhere('programa_contactos.PRCO_CORREO', 'LIKE', "%{$criterio}%")
                          ->orWhere('programa_contactos.PRCO_TELEFONO', 'LIKE', "%{$criterio}%");
                    });
                }
        
                $query->orderBy('programa_contactos.PRCO_NOMBRE');
        
                $registros = $query->get();
        
                $contactos = [];
                $programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();

                foreach ($registros as $registro) {
                    $contacto = new ProgramaContacto();
        
                    $contacto->setId($registro->prco_id);
                    $contacto->setNombre($registro->prco_nombre);
                    $contacto->setTelefono($registro->prco_telefono);
                    $contacto->setEmail($registro->prco_correo);
                    $contacto->setObservacion($registro->prco_observacion);
                    
                    if (!is_null($registro->prog_id)) {
                        $programa = $programaRepo->buscarPorID($registro->prog_id);
                        $contacto->setPrograma($programa);
                    }
        
                    $contactos[] = $contacto;
                }
                
                return $contactos;
            });
        } catch (\Exception $e) {
            Log::error("Error al listar contactos: " . $e->getMessage());
        }
        
        return $contactos;
    }
    

    /**
     * Buscar un contacto por su ID.
     */
    public function buscarPorID(int $id): ProgramaContacto {
        $cacheKey = 'contacto_' . $id; 
        
        try {
            
            $contacto = Cache::remember($cacheKey, now()->addHours(6), function () use ($id) {
                
                $registro = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.programa_contactos')
                    ->select('prco_id', 'prco_nombre', 'prco_telefono', 'prco_correo', 'prco_observacion', 'prog_id') 
                    ->where('prco_id', $id)
                    ->first();
    
                if ($registro) {
                    $contacto = new ProgramaContacto();
    
                    $contacto->setId($registro->prco_id);
                    $contacto->setNombre($registro->prco_nombre);
                    $contacto->setTelefono($registro->prco_telefono);
                    $contacto->setEmail($registro->prco_correo); 
                    $contacto->setObservacion($registro->prco_observacion);
    
                    
                    if (!is_null($registro->prog_id)) {
                        $programaRepo = FabricaDeRepositoriosOracle::getInstance()->getProgramaRepository();
                        $programa = $programaRepo->buscarPorID($registro->prog_id);
                        $contacto->setPrograma($programa);
                    }
    
                    return $contacto;
                }
    
                return null; 
            });
    
            return $contacto ?: new ProgramaContacto(); 
    
        } catch (\Exception $e) {
            Log::error("Error al buscar contacto por ID {$id}: " . $e->getMessage());
        }
    
        return new ProgramaContacto();
    }
    

    /**
     * Crear un nuevo contacto en la base de datos.
     */
    public function crear(ProgramaContacto $contacto): bool {
        try {

            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_PROGRAMA_CONTACTOS_ID.NEXTVAL AS id FROM DUAL')
                ->id;
    

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.PROGRAMA_CONTACTOS')
                ->insert([
                    'PRCO_ID'            => $nuevoID,
                    'PRCO_NOMBRE'        => $contacto->getNombre(),
                    'PRCO_TELEFONO'      => $contacto->getTelefono(),
                    'PRCO_CORREO'        => $contacto->getEmail(),
                    'PRCO_OBSERVACION'   => $contacto->getObservacion(),
                    'PROG_ID'            => $contacto->getPrograma()?->getId(),
                    'PRCO_REGISTRADOPOR' => Auth::user()->id,  
                    'PRCO_FECHACAMBIO'   => now(),  
                ]);

            $contacto->setId($nuevoID);
    
            Cache::forget('contactos_lista_cache');
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error al crear contacto: " . $e->getMessage());
            return false;
        }
    }
    
    
    /**
     * Actualizar un contacto en la base de datos.
     */
    public function actualizar(ProgramaContacto $contacto): bool {
        try {
            $filasAfectadas = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.programa_contactos')  
                ->where('prco_id', $contacto->getId())  
                ->update([
                    'prco_nombre' => $contacto->getNombre(), 
                    'prco_telefono' => $contacto->getTelefono(), 
                    'prco_correo' => $contacto->getEmail(),  
                    'prco_observacion' => $contacto->getObservacion(),
                    'prog_id' => $contacto->getPrograma()?->getId() 
                ]);
        
            Cache::forget('contactos_lista_cache');

            return true;

        } catch (\Exception $e) {
            Log::error("Error al actualizar contacto ID {$contacto->getId()}: " . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Eliminar un contacto por su ID.
     */
    public function eliminar(int $programaContactoID): bool {
        try {
            $eliminados = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.programa_contactos') 
                ->where('prco_id', $programaContactoID)  
                ->delete();

            if ($eliminados === 0) {
                Log::warning("Intento de eliminar un contacto que no existe: ID {$programaContactoID}");
                return false;
            }
            
            Cache::forget('contactos_lista_cache');

            return true;
        } catch (\Exception $e) {
            Log::error("Error al eliminar contacto ID {$programaContactoID}: " . $e->getMessage());
        }
        
        return false;
    }
    
    
}
