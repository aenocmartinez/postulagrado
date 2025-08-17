<?php

namespace Src\infrastructure\persistencia\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\Modalidad;
use Src\domain\repositories\ModalidadRepository;

class OracleModalidadRepository implements ModalidadRepository
{

    public function BuscarPorID(int $modalidadID): Modalidad
    {
        $modalidad = new Modalidad();

        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.MODALIDAD')
                ->select('MODA_ID', 'MODA_DESCRIPCION')
                ->where('MODA_ID', $modalidadID)
                ->first();

            if ($registro) {
                $modalidad->setId((int) $registro->moda_id);
                $modalidad->setNombre((string) $registro->moda_descripcion);
            }

        } catch (\Exception $e) {
            Log::error("ModalidadDao / BuscarPorID: " . $e->getMessage());
        }

        return $modalidad;
    }

    public function Listar(): array
    {
        $modalidades = [];
    
        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.MODALIDAD')
                ->select('MODA_ID', 'MODA_DESCRIPCION')
                ->orderBy('MODA_ID') // Opcional: para listar ordenadamente
                ->get();
    
            foreach ($registros as $registro) {
                $modalidad = new Modalidad();
    
                $modalidad->setId((int) $registro->moda_id);
                $modalidad->setNombre((string) $registro->moda_descripcion);
    
                $modalidades[] = $modalidad;
            }
        } catch (\Exception $e) {
            Log::error("ModalidadDao / Listar(): " . $e->getMessage());
        }
    
        return $modalidades;
    }    
}