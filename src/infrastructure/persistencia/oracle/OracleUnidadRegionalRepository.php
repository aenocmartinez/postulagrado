<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\repositories\UnidadRegionalRepository;
use Src\domain\UnidadRegional;

class OracleUnidadRegionalRepository implements UnidadRegionalRepository {

    public function BuscarPorID(int $programaID): UnidadRegional
    {
        $unidadRegional = new UnidadRegional();
    
        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.UNIDADPROGRAMA AS UNPR')
                ->join('ACADEMICO.UNIDAD AS UNID', 'UNID.UNID_ID', '=', 'UNPR.UNID_ID')
                ->select('UNID.UNID_ID', 'UNID.UNID_NOMBRE')
                ->where('UNPR.PROG_ID', $programaID)
                ->where('UNID.UNID_REGIONAL', '1')
                ->first();
    
            if ($registro) {
                $unidadRegional->setId((int) $registro->unid_id);
                $unidadRegional->setNombre((string) $registro->unid_nombre);
            }
    
        } catch (\Exception $e) {
            Log::error("UnidadRegionalDao / BuscarPorID: " . $e->getMessage());
        }
    
        return $unidadRegional;
    }       

    public function Listar(): array
    {
        $unidadesRegionales = [];
    
        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.UNIDAD')
                ->select('UNID_ID', 'UNID_NOMBRE')
                ->where('UNID_REGIONAL', '1')
                ->orderBy('UNID_NOMBRE')
                ->get();
    
            foreach ($registros as $registro) {
                $unidadRegional = new UnidadRegional();
    
                $unidadRegional->setId((int) $registro->unid_id);
                $unidadRegional->setNombre((string) $registro->unid_nombre);
    
                $unidadesRegionales[] = $unidadRegional;
            }
    
        } catch (\Exception $e) {
            Log::error("UnidadRegionalDao / Listar(): " . $e->getMessage());
        }
    
        return $unidadesRegionales;
    }
    
} 
