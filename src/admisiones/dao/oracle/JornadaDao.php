<?php

namespace Src\admisiones\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Jornada;
use Src\admisiones\repositories\JornadaRepository;
use Src\shared\di\FabricaDeRepositorios;

class JornadaDao implements JornadaRepository
{
    public function BuscarPorID(int $jornadaID): Jornada {
        $jornada = new Jornada(
            FabricaDeRepositorios::getInstance()->getJornadaRepository()
        );

        try {
            $registro = DB::connection('oracle_academico')
                ->table('ACADEMICO.JORNADA')
                ->where('JORN_ID', $jornadaID)
                ->first();

            if ($registro) {
                $jornada->setId((int) $registro->JORN_ID);
                $jornada->setNombre((string) $registro->JORN_DESCRIPCION);
            }
        } catch (\Exception $e) {
            Log::error("JornadaDao / BuscarPorID: " . $e->getMessage());
        }

        return $jornada;
    }

    public function Listar(): array {
        $jornadas = [];

        try {
            $registros = DB::connection('oracle_academico')
                ->table('ACADEMICO.JORNADA')
                ->get();

            foreach ($registros as $registro) {
                $jornada = new Jornada(
                    FabricaDeRepositorios::getInstance()->getJornadaRepository()
                );

                $jornada->setId((int) $registro->JORN_ID);
                $jornada->setNombre((string) $registro->JORN_DESCRIPCION);

                $jornadas[] = $jornada;
            }
        } catch (\Exception $e) {
            Log::error("JornadaDao / Listar(): " . $e->getMessage());
        }

        return $jornadas;
    }
}
