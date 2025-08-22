<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\programa\contacto\Contacto;
use Src\domain\repositories\ContactoRepository;

class OracleContactoRepository extends Model implements ContactoRepository {

    protected $connection = 'oracle_academpostulgrado';

    protected $table = 'ACADEMPOSTULGRADO.PROGRAMA_CONTACTOS';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'PRCO_ID';

    protected $fillable = ['PRCO_NOMBRE', 'PRCO_TELEFONO', 'PRCO_CORREO', 'PRCO_OBSERVACION', 'PROG_ID'];

    /**
     * Listar los contactos (incluye nombre del programa)
     */
    public function listar(): array
    {
        $cacheKey = 'contactos_lista_cache';

        try {
            return Cache::remember($cacheKey, now()->addHours(6), function () {
                $registros = self::query()
                    ->from('ACADEMPOSTULGRADO.PROGRAMA_CONTACTOS AS PC')
                    ->leftJoin('ACADEMICO.PROGRAMA AS PROG', 'PROG.PROG_ID', '=', 'PC.PROG_ID')
                    ->select(
                        'PC.PRCO_ID',
                        'PC.PRCO_NOMBRE',
                        'PC.PRCO_TELEFONO',
                        'PC.PRCO_CORREO',
                        'PC.PRCO_OBSERVACION',
                        'PC.PROG_ID',
                        'PROG.PROG_NOMBRE AS PROG_NOMBRE'
                    )
                    ->orderBy('PC.PRCO_NOMBRE')
                    ->get();

                $contactos = [];
                foreach ($registros as $r) {
                    $c = new Contacto();

                    $c->setId((int)   ($r->PRCO_ID ?? $r->prco_id ?? 0));
                    $c->setNombre((string)($r->PRCO_NOMBRE ?? $r->prco_nombre ?? ''));
                    $c->setTelefono((string)($r->PRCO_TELEFONO ?? $r->prco_telefono ?? ''));
                    $c->setEmail((string)($r->PRCO_CORREO ?? $r->prco_correo ?? ''));
                    $c->setObservacion($r->PRCO_OBSERVACION ?? $r->prco_observacion ?? null);

                    $c->setProgramaID((int)($r->PROG_ID ?? $r->prog_id ?? 0));
                    $c->setProgramaNombre((string)($r->PROG_NOMBRE ?? $r->prog_nombre ?? ''));

                    $contactos[] = $c;
                }

                return $contactos;
            });
        } catch (\Throwable $e) {
            Log::error('Error al listar contactos: '.$e->getMessage(), ['exception' => $e]);
            return [];
        }
    }

    /**
     * Buscar un contacto por su ID (incluye nombre del programa).
     */
    public function buscarPorID(int $id): Contacto
    {
        $cacheKey = 'contacto_' . $id;

        try {
            $contacto = Cache::remember($cacheKey, now()->addHours(6), function () use ($id) {
                $registro = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.PROGRAMA_CONTACTOS AS PC')
                    ->leftJoin('ACADEMICO.PROGRAMA AS PROG', 'PROG.PROG_ID', '=', 'PC.PROG_ID')
                    ->select(
                        'PC.PRCO_ID        AS prco_id',
                        'PC.PRCO_NOMBRE    AS prco_nombre',
                        'PC.PRCO_TELEFONO  AS prco_telefono',
                        'PC.PRCO_CORREO    AS prco_correo',
                        'PC.PRCO_OBSERVACION AS prco_observacion',
                        'PC.PROG_ID        AS prog_id',
                        'PROG.PROG_NOMBRE  AS prog_nombre'
                    )
                    ->where('PC.PRCO_ID', $id)
                    ->first();

                if (!$registro) {
                    return null;
                }

                $c = new Contacto();
                $c->setId((int)($registro->prco_id ?? 0));
                $c->setNombre((string)($registro->prco_nombre ?? ''));
                $c->setTelefono((string)($registro->prco_telefono ?? ''));
                $c->setEmail((string)($registro->prco_correo ?? ''));
                $c->setObservacion($registro->prco_observacion ?? null);

                $progId = (int)($registro->prog_id ?? 0);
                $c->setProgramaID($progId);
                $c->setProgramaNombre((string)($registro->prog_nombre ?? ''));

                return $c;
            });

            return $contacto ?: new Contacto();
        } catch (\Throwable $e) {
            Log::error("Error al buscar contacto por ID {$id}: " . $e->getMessage(), ['exception' => $e]);
            return new Contacto();
        }
    }


    /**
     * Crear un nuevo contacto en la base de datos.
     */
    public function crear(Contacto $contacto): bool {
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
                    'PROG_ID'            => $contacto->getProgramaId(),
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
    public function actualizar(Contacto $contacto): bool {
        try {
            $filasAfectadas = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.programa_contactos')  
                ->where('prco_id', $contacto->getId())  
                ->update([
                    'prco_nombre' => $contacto->getNombre(), 
                    'prco_telefono' => $contacto->getTelefono(), 
                    'prco_correo' => $contacto->getEmail(),  
                    'prco_observacion' => $contacto->getObservacion(),
                    'prog_id' => $contacto->getProgramaId() 
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
