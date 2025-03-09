<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\UnidadRegional;
use Src\admisiones\repositories\UnidadRegionalRepository;
use Src\shared\di\FabricaDeRepositorios;

class UnidadRegionalDao extends Model implements UnidadRegionalRepository {

    protected $table = 'unidad_regional';
    protected $fillable = ['nombre']; 

    public function BuscarPorID(int $unidadRegionalID): UnidadRegional {

        $unidadRegional = new UnidadRegional(
            FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository(),
        );

        try {

            $registro = self::find($unidadRegionalID);

            if ($registro) {
                $unidadRegional->setId($registro->id);
                $unidadRegional->setNombre($registro->nombre);
            }


        } catch (\Exception $e) {
            Log::error("UnidadRegionalDao / BuscarPorID: " . $e->getMessage());
        }

        return $unidadRegional;
    }

    public function Listar(): array {
        $unidadesRegionales = [];

        try {

            $registros = self::all();

            foreach($registros as $registro) {
                $unidadRegional = new UnidadRegional(
                    FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository(), 
                    $registro->id, 
                    $registro->nombre
                );

                $unidadesRegionales[] = $unidadRegional;
            }

        } catch(\Exception $e) {
            Log::error("UnidadRegionalDao / Listar(): " . $e->getMessage());
        }

        return $unidadesRegionales;
    }
}