<?php

namespace Src\infraestructure\dao\oracle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\domain\NivelEducativo;
use Src\repositories\NivelEducativoRepository;
use Src\shared\di\FabricaDeRepositorios;

use Illuminate\Support\Facades\Cache;

class NivelEducativoDao implements NivelEducativoRepository
{
    protected string $conexion = 'oracle_academico';
    protected string $tabla = 'ACADEMICO.NIVELEDUCATIVO';

    public function BuscarPorID(int $nivelEducativoID): NivelEducativo
    {
        return Cache::remember('nivel_educativo_' . $nivelEducativoID, now()->addHours(4), function () use ($nivelEducativoID) {
            $nivelEducativo = new NivelEducativo(
                FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
            );
    
            try {
                $registro = DB::connection('oracle_academico')
                    ->table('ACADEMICO.NIVELEDUCATIVO')
                    ->select('NIED_ID', 'NIED_DESCRIPCION')
                    ->where('NIED_ID', $nivelEducativoID)
                    ->first();
    
                if ($registro) {
                    $nivelEducativo->setId((int) $registro->nied_id);
                    $nivelEducativo->setNombre((string) $registro->nied_descripcion);
                }
            } catch (\Exception $e) {
                Log::error("NivelEducativoDao / BuscarPorID: " . $e->getMessage());
            }
    
            return $nivelEducativo;
        });
    }
    

    public function Listar(): array
    {
        return Cache::remember('nivel_educativo_listar', now()->addHours(8), function () {
            $niveles = [];
    
            try {
                $registros = DB::connection($this->conexion)
                    ->table($this->tabla)
                    ->select('NIED_ID', 'NIED_DESCRIPCION')
                    ->orderBy('NIED_DESCRIPCION')
                    ->get();
    
                foreach ($registros as $registro) {
                    $nivel = new NivelEducativo(
                        FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
                    );
    
                    $nivel->setId((int) $registro->nied_id);
                    $nivel->setNombre((string) $registro->nied_descripcion);
    
                    $niveles[] = $nivel;
                }
    
            } catch (\Exception $e) {
                Log::error("NivelEducativoDao / Listar: " . $e->getMessage());
            }
    
            return $niveles;
        });
    }
    
    
}
