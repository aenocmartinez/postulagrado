<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Src\admisiones\domain\Jornada;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\UnidadRegional;
use Src\admisiones\repositories\ProgramaRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaDao extends Model implements ProgramaRepository {

    protected $table = 'programas';
    protected $fillable = ['programa_id', 'nombre', 'codigo', 'snies']; 

    
    public function metodologia(): Metodologia
    {
        $metodologia = new Metodologia(
            FabricaDeRepositorios::getInstance()->getMetodologiaRepository()
        );
    
        $registro = $this->belongsTo(MetodologiaDao::class, 'metodologia_id')->first();
    
        if ($registro) {
            $metodologia->setId($registro->id);
            $metodologia->setNombre($registro->nombre);
        }
    
        return $metodologia;
    }

    public function nivelEducativo(): NivelEducativo {
        $nivelEducativo = new NivelEducativo(
            FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
        );
    
        $registro = $this->belongsTo(MetodologiaDao::class, 'nivel_educativo_id')->first();
    
        if ($registro) {
            $nivelEducativo->setId($registro->id);
            $nivelEducativo->setNombre($registro->nombre);
        }
    
        return $nivelEducativo;        
    }
    
    public function modalidad(): Modalidad {
        $modalidad = new Modalidad(
            FabricaDeRepositorios::getInstance()->getModalidadRepository()
        );
    
        $registro = $this->belongsTo(MetodologiaDao::class, 'modalidad_id')->first();
    
        if ($registro) {
            $modalidad->setId($registro->id);
            $modalidad->setNombre($registro->nombre);
        }
    
        return $modalidad;        
    }

    public function jornada(): Jornada {
        $jornada = new Jornada(
            FabricaDeRepositorios::getInstance()->getJornadaRepository()
        );
    
        $registro = $this->belongsTo(MetodologiaDao::class, 'jornada_id')->first();
    
        if ($registro) {
            $jornada->setId($registro->id);
            $jornada->setNombre($registro->nombre);
        }
    
        return $jornada;        
    }

    public function unidadRegional(): UnidadRegional {

        $unidadRegional = new UnidadRegional(
            FabricaDeRepositorios::getInstance()->getUnidadRegionalRepository()
        );
    
        $registro = $this->belongsTo(MetodologiaDao::class, 'unidad_regional_id')->first();
    
        if ($registro) {
            $unidadRegional->setId($registro->id);
            $unidadRegional->setNombre($registro->nombre);
        }
    
        return $unidadRegional;        
    }

}