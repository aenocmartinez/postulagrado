<?php

namespace Src\admisiones\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\shared\di\FabricaDeRepositorios;

class NivelEducativoDao implements NivelEducativoRepository
{
    protected string $conexion = 'oracle_academico';
    protected string $tabla = 'ACADEMICO.NIVELEDUCATIVO';

    public function BuscarPorID(int $nivelEducativoID): NivelEducativo
    {
        $nivelEducativo = new NivelEducativo(
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
        );

        try {
            $registro = DB::connection($this->conexion)
                ->table($this->tabla)
                ->where('NIED_ID', $nivelEducativoID)
                ->first();

            if ($registro) {
                $nivelEducativo->setId((int) $registro->NIED_ID);
                $nivelEducativo->setNombre((string) $registro->NIED_DESCRIPCION);
            }

        } catch (\Exception $e) {
            Log::error("NivelEducativoDao / BuscarPorID: " . $e->getMessage());
        }

        return $nivelEducativo;
    }

    public function Listar(): array
    {
        $niveles = [];

        try {
            $registros = DB::connection($this->conexion)
                ->table($this->tabla)
                ->orderBy('NIED_DESCRIPCION')
                ->get();

            foreach ($registros as $registro) {
                $nivel = new NivelEducativo(
                    FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
                );

                $nivel->setId((int) $registro->NIED_ID);
                $nivel->setNombre((string) $registro->NIED_DESCRIPCION);

                $niveles[] = $nivel;
            }

        } catch (\Exception $e) {
            Log::error("NivelEducativoDao / Listar: " . $e->getMessage());
        }

        return $niveles;
    }
}
