<?php

namespace Src\admisiones\infraestructure\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\shared\di\FabricaDeRepositorios;

class NivelEducativoDao extends Model implements NivelEducativoRepository {

    protected $table = 'nivel_educativo';
    protected $fillable = ['nombre']; 

    public function BuscarPorID(int $nivelEducativoID): NivelEducativo {

        $nivelEducativo = new NivelEducativo(
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(),
        );

        try {

            $registro = self::find($nivelEducativoID);

            if ($registro) {
                $nivelEducativo->setId($registro->id);
                $nivelEducativo->setNombre($registro->nombre);
            }


        } catch (\Exception $e) {
            Log::error("NivelEducativoDao / BuscarPorID: " . $e->getMessage());
        }

        return $nivelEducativo;
    }

    public function Listar(): array {
        $niveles = [];

        try {

            $registros = self::all();

            foreach($registros as $registro) {
                $nivel = new NivelEducativo(
                    FabricaDeRepositorios::getInstance()->getNivelEducativoRepository(), 
                    $registro->id, 
                    $registro->nombre
                );

                $niveles[] = $nivel;
            }

        } catch(\Exception $e) {
            Log::error("NivelEducativoDao / Listar(): " . $e->getMessage());
        }

        return $niveles;
    }
}