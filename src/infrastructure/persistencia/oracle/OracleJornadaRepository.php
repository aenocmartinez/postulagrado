<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\Jornada;
use Src\domain\repositories\JornadaRepository;

class OracleJornadaRepository implements JornadaRepository
{
    public function BuscarPorID(int $jornadaID): Jornada
    {
        $jornada = new Jornada();
    
        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.JORNADA')
                ->select('JORN_ID', 'JORN_DESCRIPCION')
                ->where('JORN_ID', $jornadaID)
                ->first();
    
            if ($registro) {
                $jornada->setId((int) $registro->jorn_id);
                $jornada->setNombre((string) $registro->jorn_descripcion);
            }
        } catch (\Exception $e) {
            Log::error("JornadaDao / BuscarPorID: " . $e->getMessage());
        }
    
        return $jornada;
    }       

    public function Listar(): array
    {
        $jornadas = [];
    
        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.JORNADA')
                ->select('JORN_ID', 'JORN_DESCRIPCION')
                ->orderBy('JORN_ID') // Opcional: para listar ordenadamente
                ->get();
    
            foreach ($registros as $registro) {
                $jornada = new Jornada();

                $jornada->setId((int) $registro->jorn_id);
                $jornada->setNombre((string) $registro->jorn_descripcion);
    
                $jornadas[] = $jornada;
            }
        } catch (\Exception $e) {
            Log::error("JornadaDao / Listar(): " . $e->getMessage());
        }
    
        return $jornadas;
    }    
}
