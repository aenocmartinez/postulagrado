<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Jornada;
use Src\admisiones\repositories\JornadaRepository;
use Src\shared\di\FabricaDeRepositorios;

class JornadaDao extends Model implements JornadaRepository
{
    protected $table = 'jornadas';
    protected $fillable = ['nombre'];    

    public function BuscarPorID(int $metodologiaID): Jornada {
        $jornada = new Jornada(
            FabricaDeRepositorios::getInstance()->getJornadaRepository()
        );

        try {

            $registro = self::find($metodologiaID);

            if ($registro) {
                $jornada->setId($registro->id);
                $jornada->setNombre($registro->nombre);
            }


        } catch (\Exception $e) {
            Log::error("JornadaDao / BuscarPorID: " . $e->getMessage());
        }

        return $jornada;
    }

    public function Listar(): array {

        $joranadas = [];

        try {

            $registros = self::all();

            foreach($registros as $registro) {
                $jornada = new Jornada(
                    FabricaDeRepositorios::getInstance()->getJornadaRepository(), 
                    $registro->id, 
                    $registro->nombre
                );

                $jornadas[] = $jornada;
            }

        } catch(\Exception $e) {
            Log::error("JornadaDao / Listar(): " . $e->getMessage());
        }

        return $joranadas;
    }
}