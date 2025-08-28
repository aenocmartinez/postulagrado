<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Src\domain\Notificacion;
use Src\domain\proceso\Proceso;
use Src\domain\repositories\NotificacionRepository;
use Src\shared\di\FabricaDeRepositoriosOracle;

class OracleNotificacionRepository extends Model implements NotificacionRepository
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

                    $procesoDao = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
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
            
            $procesoDao = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
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
            Cache::forget("notificaciones_listado_{$notificacion->getProceso()->getId()}");

            $this->olvidarCacheNotificacionesUsuarios($notificacion->getDestinatarios());

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
            Cache::forget("notificaciones_listado_".$notificacion->getProceso()->getId());

            $this->olvidarCacheNotificacionesUsuarios($notificacion->getDestinatarios());
    
            return true;
        } catch (\Exception $e) {
            Log::error("Error en actualizar notificación ID {$notificacion->getId()}: " . $e->getMessage());
            return false;
        }
    }

    public function listarPorUsuario(string $email): array
    {
        return Cache::remember("notificaciones_usuario_{$email}", now()->addHours(4), function () use ($email) {
            $procesoDao = FabricaDeRepositoriosOracle::getInstance()->getProcesoRepository();
            $notificaciones = [];

            try {
                $registros = DB::connection('oracle_academpostulgrado')
                    ->table('ACADEMPOSTULGRADO.NOTIFICACION AS N')
                    ->leftJoin('ACADEMPOSTULGRADO.NOTIFICACION_LEIDA AS NL', function ($join) use ($email) {
                        $join->on('N.NOTI_ID', '=', 'NL.NOTI_ID')
                            ->where('NL.USUA_CORREO', '=', $email);
                    })
                    ->select(
                        'N.NOTI_ID',
                        'N.PROC_ID',
                        'N.NOTI_FECHA',
                        'N.NOTI_ASUNTO',
                        'N.NOTI_CANAL',
                        'N.NOTI_MENSAJE',
                        'N.NOTI_DESTINATARIOS',
                        'N.NOTI_ESTADO',
                        DB::raw("CASE WHEN NL.NOTL_ID IS NOT NULL THEN 1 ELSE 0 END AS fue_leida")
                    )
                    ->whereRaw("DBMS_LOB.INSTR(N.NOTI_DESTINATARIOS, ?) > 0", [$email])
                    ->where("N.NOTI_ESTADO", 'ENVIADA')
                    ->orderBy('N.NOTI_FECHA', 'desc')
                    ->get();

                    // $registros->dd();
                
                foreach ($registros as $registro) {
                    $notificacion = new Notificacion($this);
                    $notificacion->setId($registro->noti_id);
                    $notificacion->setFechaCreacion($registro->noti_fecha);
                    $notificacion->setAsunto($registro->noti_asunto);
                    $notificacion->setCanal($registro->noti_canal);
                    $notificacion->setMensaje($registro->noti_mensaje);
                    $notificacion->setDestinatarios($registro->noti_destinatarios);
                    $notificacion->setEstado($registro->noti_estado);
                    $notificacion->setFueLeida((bool) $registro->fue_leida); 

                    $proceso = $procesoDao->buscarProcesoPorId($registro->proc_id);
                    $notificacion->setProceso($proceso);

                    $notificaciones[] = $notificacion;
                }
            } catch (\Exception $e) {
                Log::error("Error en listar notificaciones por usuario ($email): " . $e->getMessage());
            }

            return $notificaciones;
        });
    }

    public function marcarComoLeida(int $notificacionID, string $emailUsuario): bool
    {
        try {
            $conexion = DB::connection('oracle_academpostulgrado');

            $existe = $conexion->table('ACADEMPOSTULGRADO.NOTIFICACION_LEIDA')
                ->where('NOTI_ID', $notificacionID)
                ->where('USUA_CORREO', $emailUsuario)
                ->exists();

            if (!$existe) {
                $conexion->table('ACADEMPOSTULGRADO.NOTIFICACION_LEIDA')->insert([
                    'NOTI_ID' => $notificacionID,
                    'USUA_CORREO' => $emailUsuario,
                    'NOTL_FECHALECTURA' => now(),
                ]);
            }

            Cache::forget("notificaciones_usuario_{$emailUsuario}");

            return true;
        } catch (\Throwable $e) {
            Log::error("Error al marcar como leída la notificación {$notificacionID} para {$emailUsuario}: " . $e->getMessage());
            return false;
        }
    }

    private function olvidarCacheNotificacionesUsuarios(string $destinatarios): void
    {
        $emails = explode(',', $destinatarios);

        foreach ($emails as $email) {
            $email = trim($email);
            if (!empty($email)) {
                Cache::forget("notificaciones_usuario_{$email}");
            }
        }
    }

public function listarNotificacionesPorProceso(int $procesoID): array
{
    $cacheKey = "notificaciones_listado_{$procesoID}";

    return Cache::remember($cacheKey, now()->addHours(4), function () use ($procesoID) {
        $notificaciones = [];

        try {
            $rows = DB::connection('oracle_academpostulgrado')
                ->table('ACADEMPOSTULGRADO.NOTIFICACION AS N')
                ->leftJoin('ACADEMPOSTULGRADO.PROCESO AS P', 'P.PROC_ID', '=', 'N.PROC_ID')
                ->leftJoin('ACADEMICO.NIVELEDUCATIVO AS NIED', 'NIED.NIED_ID', '=', 'P.NIED_ID')
                ->where('N.PROC_ID', $procesoID)
                ->select(
                    'N.NOTI_ID             AS noti_id',
                    'N.NOTI_FECHA          AS noti_fecha',
                    'N.NOTI_ASUNTO         AS noti_asunto',
                    'N.NOTI_CANAL          AS noti_canal',
                    'N.NOTI_MENSAJE        AS noti_mensaje',
                    'N.NOTI_DESTINATARIOS  AS noti_destinatarios',
                    'N.NOTI_ESTADO         AS noti_estado',
                    'N.PROC_ID             AS proc_id',
                    'P.PROC_NOMBRE         AS proc_nombre',
                    'P.PROC_ESTADO         AS proc_estado',
                    'P.NIED_ID             AS nied_id',
                    'NIED.NIED_DESCRIPCION AS nied_nombre'
                )
                ->orderBy('N.NOTI_FECHA', 'desc')
                ->get();

            foreach ($rows as $r) {

                $n = new Notificacion();
                $n->setId((int)($r->noti_id ?? 0));
                $n->setFechaCreacion($r->noti_fecha); 
                // $n->setFechaCreacion(new \DateTimeImmutable((string)$r->noti_fecha));

                $n->setAsunto((string)($r->noti_asunto ?? ''));
                $n->setCanal((string)($r->noti_canal ?? ''));
                $n->setMensaje((string)($r->noti_mensaje ?? ''));
                $n->setDestinatarios((string)($r->noti_destinatarios ?? ''));
                $n->setEstado((string)($r->noti_estado ?? ''));
                $p = new Proceso();
                $p->setId((int)($r->proc_id ?? 0));
                $p->setNombre((string)($r->proc_nombre ?? ''));
                $p->setEstado((string)($r->proc_estado ?? ''));
                $p->setNivelEducativoID((int)($r->nied_id ?? 0));
                $p->setNivelEducativoNombre((string)($r->nied_nombre ?? ''));

                $n->setProceso($p);
                $notificaciones[] = $n;
            }
        } catch (\Throwable $e) {
            Log::error("Error en listar notificaciones para proceso {$procesoID}: ".$e->getMessage(), ['exception' => $e]);
        }

        return $notificaciones;
    });
} 

}
