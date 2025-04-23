<?php

namespace Src\admisiones\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\repositories\MetodologiaRepository;
use Src\shared\di\FabricaDeRepositorios;

class MetodologiaDao implements MetodologiaRepository
{
    public function BuscarPorID(int $metodologiaID): Metodologia
    {
        $metodologia = new Metodologia(
            FabricaDeRepositorios::getInstance()->getMetodologiaRepository()
        );
    
        try {
            $registro = DB::connection('oracle_academico')->selectOne(
                "
                SELECT METO_ID, METO_DESCRIPCION
                FROM ACADEMICO.METODOLOGIA
                WHERE METO_ID = :id
                ",
                ['id' => $metodologiaID]
            );
    
            if ($registro) {
                $metodologia->setId((int) $registro->METO_ID);
                $metodologia->setNombre((string) $registro->METO_DESCRIPCION);
            }
        } catch (\Exception $e) {
            Log::error("MetodologiaDao / BuscarPorID: " . $e->getMessage());
        }
    
        return $metodologia;
    }
    

    public function Listar(): array {
        $metodologias = [];

        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.METODOLOGIA')
                ->get();

            foreach ($registros as $registro) {
                $metodologia = new Metodologia(
                    FabricaDeRepositorios::getInstance()->getMetodologiaRepository()
                );

                $metodologia->setId((int) $registro->METO_ID);
                $metodologia->setNombre((string) $registro->METO_DESCRIPCION);

                $metodologias[] = $metodologia;
            }
        } catch (\Exception $e) {
            Log::error("MetodologiaDao / Listar(): " . $e->getMessage());
        }

        return $metodologias;
    }
}
