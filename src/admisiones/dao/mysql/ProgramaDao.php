<?php

namespace Src\admisiones\dao\mysql;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\Jornada;
use Src\admisiones\domain\Metodologia;
use Src\admisiones\domain\Modalidad;
use Src\admisiones\domain\NivelEducativo;
use Src\admisiones\domain\Programa;
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
    
        $registro = $this->belongsTo(NivelEducativoDao::class, 'nivel_educativo_id')->first();
    
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
    
        $registro = $this->belongsTo(ModalidadDao::class, 'modalidad_id')->first();
    
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
    
        $registro = $this->belongsTo(JornadaDao::class, 'jornada_id')->first();
    
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
    
        $registro = $this->belongsTo(UnidadRegionalDao::class, 'unidad_regional_id')->first();
    
        if ($registro) {
            $unidadRegional->setId($registro->id);
            $unidadRegional->setNombre($registro->nombre);
        }
    
        return $unidadRegional;        
    }

    public function buscarProgramasPorNivelEducativo(string $nombreNivelEducativo): array {
        $programas = [];
    
        $nivelEducativoID = 1;
        if (strtolower(trim($nombreNivelEducativo)) === "postgrado") {
            $nivelEducativoID = 2;
        }
    
        try {
            $programasDao = ProgramaDao::where('nivel_educativo_id', $nivelEducativoID)->get();
    
            foreach ($programasDao as $programaDao) {
                $programa = new Programa(
                    FabricaDeRepositorios::getInstance()->getProgramaRepository()
                );
    
                $programa->setId($programaDao->id);
                $programa->setNombre($programaDao->nombre);
                $programa->setCodigo($programaDao->codigo);
                $programa->setSnies($programaDao->snies);
    
                $nivelEducativo = new NivelEducativo(
                    FabricaDeRepositorios::getInstance()->getNivelEducativoRepository()
                );
                $nivelEducativo->setId($nivelEducativoID);
                $nivelEducativo->setNombre(ucfirst(strtolower($nombreNivelEducativo))); // Formatear nombre
                $programa->setNivelEducativo($nivelEducativo);
    
                $programas[] = $programa;
            }
        } catch (Exception $e) {
            Log::error("Error al buscar programas por nivel educativo '{$nombreNivelEducativo}': " . $e->getMessage());
        }
    
        return $programas;
    }
    
    public function buscarPorID(int $programaID): Programa {

        try {
            $programaDao = ProgramaDao::find($programaID);
    
            if (!$programaDao) {
                Log::warning("No se encontró el programa con ID {$programaID}");
                return new Programa(
                    FabricaDeRepositorios::getInstance()->getProgramaRepository()
                );
            }
    
            $programa = new Programa(
                FabricaDeRepositorios::getInstance()->getProgramaRepository()
            );

            $programa->setId($programaDao->id);
            $programa->setNombre($programaDao->nombre);
            $programa->setCodigo($programaDao->codigo);
            $programa->setSnies($programaDao->snies);
            $programa->setModalidad($programaDao->modalidad());
            $programa->setUnidadRegional($programaDao->unidadRegional());
            $programa->setNivelEducativo($programaDao->nivelEducativo());
            $programa->setMetodologia($programaDao->metodologia());
            $programa->setJornada($programaDao->jornada());
    
        } catch (\Exception $e) {
            Log::error("Error al buscar el programa por ID {$programaID}: " . $e->getMessage());
            return new Programa(FabricaDeRepositorios::getInstance()->getProgramaRepository());
        }

        return $programa;
    }
    
}