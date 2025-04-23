<?php

namespace Src\admisiones\infraestructure\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\repositories\ModalidadRepository;
use Src\shared\di\FabricaDeRepositorios;

class ModalidadDao extends Model implements ModalidadRepository
{
    protected $table = 'modalidades';
    protected $fillable = ['nombre'];    

    public function BuscarPorID(int $modalidadID): Modalidad {
        $modalidad = new Modalidad(
            FabricaDeRepositorios::getInstance()->getModalidadRepository()
        );

        try {

            $registro = self::find($modalidadID);

            if ($registro) {
                $modalidad->setId($registro->id);
                $modalidad->setNombre($registro->nombre);
            }


        } catch (\Exception $e) {
            Log::error("ModalidadDao / BuscarPorID: " . $e->getMessage());
        }

        return $modalidad;
    }

    public function Listar(): array {

        $modalidades = [];

        try {

            $registros = self::all();

            foreach($registros as $registro) {
                $modalidad = new Modalidad(
                    FabricaDeRepositorios::getInstance()->getModalidadRepository(), 
                    $registro->id, 
                    $registro->nombre
                );

                $modalidades[] = $modalidad;
            }

        } catch(\Exception $e) {
            Log::error("ModalidadDao / Listar(): " . $e->getMessage());
        }

        return $modalidades;
    }
}