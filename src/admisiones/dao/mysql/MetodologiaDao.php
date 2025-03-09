<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\repositories\MetodologiaRepository;
use Src\shared\di\FabricaDeRepositorios;

class MetodologiaDao extends Model implements MetodologiaRepository
{
    protected $table = 'metodologias';
    protected $fillable = ['nombre'];    

    public function BuscarPorID(int $metodologiaID): Metodologia {
        $metodologia = new Metodologia(
            FabricaDeRepositorios::getInstance()->getMetodologiaRepository()
        );

        try {

            $registro = self::find($metodologiaID);

            if ($registro) {
                $metodologia->setId($registro->id);
                $metodologia->setNombre($registro->nombre);
            }


        } catch (\Exception $e) {
            Log::error("MetodologiaDao / BuscarPorID: " . $e->getMessage());
        }

        return $metodologia;
    }

    public function Listar(): array {

        $metodologias = [];

        try {

            $registros = self::all();

            foreach($registros as $registro) {
                $metodologia = new Metodologia(
                    FabricaDeRepositorios::getInstance()->getMetodologiaRepository(), 
                    $registro->id, 
                    $registro->nombre
                );

                $metodologias[] = $metodologia;
            }

        } catch(\Exception $e) {
            Log::error("MetodologiaDao / Listar(): " . $e->getMessage());
        }

        return $metodologias;
    }
}