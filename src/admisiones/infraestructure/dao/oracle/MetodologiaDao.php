<?php

namespace Src\admisiones\infraestructure\dao\oracle;

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
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.METODOLOGIA')
                ->select('METO_ID', 'METO_DESCRIPCION')
                ->where('METO_ID', $metodologiaID)
                ->first();
    
            if ($registro) {
                $metodologia->setId((int) $registro->meto_id);
                $metodologia->setNombre((string) $registro->meto_descripcion);                
            }
        } catch (\Exception $e) {
            Log::error("MetodologiaDao / BuscarPorID: " . $e->getMessage());
        }
    
        return $metodologia;
    }    

    public function Listar(): array
    {
        $metodologias = [];
    
        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.METODOLOGIA')
                ->select('METO_ID', 'METO_DESCRIPCION')
                ->orderBy('METO_ID') // Opcional, por si quieres ordenarlo
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
