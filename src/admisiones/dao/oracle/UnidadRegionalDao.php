<?php

namespace Src\admisiones\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\UnidadRegional;
use Src\admisiones\repositories\UnidadRegionalRepository;
use Src\shared\di\FabricaDeRepositorios;

class UnidadRegionalDao implements UnidadRegionalRepository {

    public function BuscarPorID(int $programaID): UnidadRegional {
        $unidadRegional = new UnidadRegional(
            FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository()
        );

        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.UNIDADPROGRAMA AS UNPR')
                ->join('ACADEMICO.UNIDAD AS UNID', 'UNID.UNID_ID', '=', 'UNPR.UNID_ID')
                ->select('UNID.UNID_ID AS UNID_ID', 'UNID.UNID_NOMBRE AS UNID_NOMBRE')
                ->where('UNPR.PROG_ID', $programaID)
                ->where('UNID.UNID_REGIONAL', '1')
                ->first();

            if ($registro) {
                $unidadRegional->setId((int) $registro->UNID_ID);
                $unidadRegional->setNombre((string) $registro->UNID_NOMBRE);
            }

        } catch (\Exception $e) {
            Log::error("UnidadRegionalDao / BuscarPorID: " . $e->getMessage());
        }

        return $unidadRegional;
    }

    public function Listar(): array {
        $unidadesRegionales = [];

        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.UNIDAD')
                ->where('UNID_REGIONAL', '1')
                ->get();

            foreach($registros as $registro) {
                $unidadRegional = new UnidadRegional(
                    FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository()
                );

                $unidadRegional->setId((int) $registro->UNID_ID);
                $unidadRegional->setNombre((string) $registro->UNID_NOMBRE);

                $unidadesRegionales[] = $unidadRegional;
            }

        } catch(\Exception $e) {
            Log::error("UnidadRegionalDao / Listar(): " . $e->getMessage());
        }

        return $unidadesRegionales;
    }
} 
