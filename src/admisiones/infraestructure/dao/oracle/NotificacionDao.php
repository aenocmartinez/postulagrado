<?php

namespace Src\admisiones\infraestructure\dao\oracle;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Src\admisiones\domain\Notificacion;
use Src\admisiones\repositories\NotificacionRepository;
use Src\shared\di\FabricaDeRepositorios;

class NotificacionDao extends Model implements NotificacionRepository
{
    protected $connection = 'oracle_academpostulgrado';
    protected $table = 'ACADEMPOSTULGRADO.NOTIFICACION';
    protected $primaryKey = 'NOTI_ID';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['NOTI_FECHA', 'NOTI_ASUNTO', 'NOTI_CANAL', 'NOTI_MENSAJE', 'NOTI_DESTINATARIOS', 'NOTI_ESTADO', 'PROC_ID'];

    public function buscarPorID(int $id): Notificacion
    {
        return Cache::remember('notificacion_' . $id, now()->addHours(4), function () use ($id) {
            $notificacion = new Notificacion($this);
    
            try {
                $registro = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.NOTIFICACION')
                    ->select('NOTI_ID', 'NOTI_FECHA', 'NOTI_ASUNTO', 'NOTI_CANAL', 'NOTI_MENSAJE', 'NOTI_DESTINATARIOS', 'NOTI_ESTADO', 'PROC_ID')
                    ->where('NOTI_ID', $id)
                    ->first();
    
                if ($registro) {
                    $notificacion->setId($registro->noti_id);
                    $notificacion->setFechaCreacion($registro->noti_fecha);
                    $notificacion->setAsunto($registro->noti_asunto);
                    $notificacion->setCanal($registro->noti_canal);
                    $notificacion->setMensaje($registro->noti_mensaje);
                    $notificacion->setDestinatarios($registro->noti_destinatarios);
                    $notificacion->setEstado($registro->noti_estado);

                    $procesoDao = FabricaDeRepositorios::getInstance()->getProcesoRepository();
                    $proceso = $procesoDao->buscarProcesoPorId($registro->proc_id);
                    $notificacion->setProceso($proceso);
                }
            } catch (\Exception $e) {
                Log::error("Error en buscarPorID({$id}): " . $e->getMessage());
            }
    
            return $notificacion;
        });
    }
    

    public function listar(): array
    {
        return Cache::remember('notificaciones_listado', now()->addHours(4), function () {
            
            $procesoDao = FabricaDeRepositorios::getInstance()->getProcesoRepository();
            $notificaciones = [];

            try {
                $registros = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.NOTIFICACION')
                    ->select('NOTI_ID', 'NOTI_FECHA', 'NOTI_ASUNTO', 'NOTI_CANAL', 'NOTI_MENSAJE', 'NOTI_DESTINATARIOS', 'NOTI_ESTADO', 'PROC_ID')
                    ->orderBy('NOTI_FECHA', 'desc')
                    ->get();

                foreach ($registros as $registro) {
                    $notificacion = new Notificacion($this);
                    $notificacion->setId($registro->noti_id);
                    $notificacion->setFechaCreacion($registro->noti_fecha);
                    $notificacion->setAsunto($registro->noti_asunto);
                    $notificacion->setCanal($registro->noti_canal);
                    $notificacion->setMensaje($registro->noti_mensaje);
                    $notificacion->setDestinatarios($registro->noti_destinatarios);
                    $notificacion->setEstado($registro->noti_estado);

                    $proceso = $procesoDao->buscarProcesoPorId($registro->proc_id);
                    $notificacion->setProceso($proceso);

                    $notificaciones[] = $notificacion;
                }
            } catch (\Exception $e) {
                Log::error("Error en listar notificaciones: " . $e->getMessage());
            }

            return $notificaciones;
        });
    }

    public function crear(Notificacion $notificacion): bool
    {
        try {
            $nuevoID = DB::connection('oracle_academpostulgrado')
                ->selectOne('SELECT ACADEMPOSTULGRADO.S_NOTIFICACION_ID.NEXTVAL AS id FROM DUAL')
                ->id;

            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.NOTIFICACION')
                ->insert([
                    'NOTI_ID'            => $nuevoID,
                    'NOTI_FECHA'         => $notificacion->getFechaCreacion(),
                    'NOTI_ASUNTO'        => $notificacion->getAsunto(),
                    'NOTI_CANAL'         => $notificacion->getCanal(),
                    'NOTI_MENSAJE'       => $notificacion->getMensaje(),
                    'NOTI_DESTINATARIOS' => $notificacion->getDestinatarios(),
                    'NOTI_ESTADO'        => $notificacion->getEstado(),
                    'PROC_ID'            => $notificacion->getProceso()->getId(),
                    'NOTI_REGISTRADOPOR' => Auth::user()->id ?? 'system',
                    'NOTI_FECHACAMBIO'   => now(),
                ]);

            $notificacion->setId($nuevoID);

            Cache::forget('notificaciones_listado');
            Cache::forget('notificacion_' . $notificacion->getId());
            Cache::forget("notificaciones_listado_{$notificacion->getId()}");            

            return true;
        } catch (\Exception $e) {
            Log::error("Error en crear notificación: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(Notificacion $notificacion): bool
    {
        try {
            DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.NOTIFICACION')
                ->where('NOTI_ID', $notificacion->getId())
                ->update([
                    'NOTI_FECHA'         => $notificacion->getFechaCreacion(),
                    'NOTI_ASUNTO'        => $notificacion->getAsunto(),
                    'NOTI_CANAL'         => $notificacion->getCanal(),
                    'NOTI_MENSAJE'       => $notificacion->getMensaje(),
                    'NOTI_DESTINATARIOS' => $notificacion->getDestinatarios(),
                    'NOTI_ESTADO'        => $notificacion->getEstado(),
                    'PROC_ID'            => $notificacion->getProceso()->getId(),
                    'NOTI_FECHACAMBIO'   => now(),
                    'NOTI_REGISTRADOPOR' => Auth::user()->id ?? 'system',
                ]);
    
            Cache::forget('notificaciones_listado');
            Cache::forget('notificacion_' . $notificacion->getId());
            Cache::forget("notificaciones_listado_{$notificacion->getId()}");

    
            return true;
        } catch (\Exception $e) {
            Log::error("Error en actualizar notificación ID {$notificacion->getId()}: " . $e->getMessage());
            return false;
        }
    }
    
}
