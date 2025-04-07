<?php

namespace Src\admisiones\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\repositories\ModalidadRepository;
use Src\shared\di\FabricaDeRepositorios;

class ModalidadDao implements ModalidadRepository
{
    public function BuscarPorID(int $modalidadID): Modalidad {
        $modalidad = new Modalidad(
            FabricaDeRepositorios::getInstance()->getModalidadRepository()
        );

        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.MODALIDAD')
                ->where('MODA_ID', $modalidadID)
                ->first();

            if ($registro) {
                $modalidad->setId((int) $registro->MODA_ID);
                $modalidad->setNombre((string) $registro->MODA_DESCRIPCION);
            }
        } catch (\Exception $e) {
            Log::error("ModalidadDao / BuscarPorID: " . $e->getMessage());
        }

        return $modalidad;
    }

    public function Listar(): array {
        $modalidades = [];

        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.MODALIDAD')
                ->get();

            foreach ($registros as $registro) {
                $modalidad = new Modalidad(
                    FabricaDeRepositorios::getInstance()->getModalidadRepository()
                );

                $modalidad->setId((int) $registro->MODA_ID);
                $modalidad->setNombre((string) $registro->MODA_DESCRIPCION);

                $modalidades[] = $modalidad;
            }
        } catch (\Exception $e) {
            Log::error("ModalidadDao / Listar(): " . $e->getMessage());
        }

        return $modalidades;
    }
}