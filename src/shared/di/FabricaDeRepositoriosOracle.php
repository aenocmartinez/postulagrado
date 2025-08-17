<?php

namespace Src\shared\di;

use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\EstudianteRepository;
use Src\domain\repositories\JornadaRepository;
use Src\domain\repositories\MetodologiaRepository;
use Src\domain\repositories\ModalidadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\NotificacionRepository;
use Src\domain\repositories\ProcesoDocumentoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaContactoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\domain\repositories\UnidadRegionalRepository;
use Src\infrastructure\persistencia\oracle\OracleActividadRepository;
use Src\infrastructure\persistencia\oracle\OracleEnlaceActualizacionRepository;
use Src\infrastructure\persistencia\oracle\OracleEstudianteRepository;
use Src\infrastructure\persistencia\oracle\OracleJornadaRepository;
use Src\infrastructure\persistencia\oracle\OracleMetodologiaRepository;
use Src\infrastructure\persistencia\oracle\OracleModalidadRepository;
use Src\infrastructure\persistencia\oracle\OracleNivelEducativoRepository;
use Src\infrastructure\persistencia\oracle\OracleNotificacionRepository;
use Src\infrastructure\persistencia\oracle\OracleProcesoDocumentoRepository;
use Src\infrastructure\persistencia\oracle\OracleProcesoRepository;
use Src\infrastructure\persistencia\oracle\OracleProgramaContactoRepository;
use Src\infrastructure\persistencia\oracle\OracleProgramaRepository;
use Src\infrastructure\persistencia\oracle\OracleUnidadRegionalRepository;

class FabricaDeRepositoriosOracle 
{
    private static ?FabricaDeRepositoriosOracle $instance = null;

    private ProcesoRepository $procesoRepo;
    private ActividadRepository $actividadRepo;
    private MetodologiaRepository $metodologiaRepo;
    private ModalidadRepository $modalidadRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private JornadaRepository $jornadaRepo;
    private UnidadRegionalRepository $unidadRegionalRepo;
    private ProgramaRepository $programaRepo;
    private ProgramaContactoRepository $programaContactoRepo;
    private NotificacionRepository $notificacionRepo;
    private ProcesoDocumentoRepository $procesoDocumentoRepo;
    private EstudianteRepository $estudianteRepo;
    private EnlaceActualizacionRepository $enlaceActualizacionRepo;

    public function __construct()
    {
        $this->procesoRepo = new OracleProcesoRepository();
        $this->actividadRepo = new OracleActividadRepository();
        $this->metodologiaRepo = new OracleMetodologiaRepository();
        $this->modalidadRepo = new OracleModalidadRepository();
        $this->nivelEducativoRepo = new OracleNivelEducativoRepository();
        $this->jornadaRepo = new OracleJornadaRepository();
        $this->unidadRegionalRepo = new OracleUnidadRegionalRepository();
        $this->programaRepo = new OracleProgramaRepository();
        $this->programaContactoRepo = new OracleProgramaContactoRepository();
        $this->notificacionRepo = new OracleNotificacionRepository();
        $this->procesoDocumentoRepo = new OracleProcesoDocumentoRepository();
        $this->estudianteRepo = new OracleEstudianteRepository();
        $this->enlaceActualizacionRepo = new OracleEnlaceActualizacionRepository();
    }

    public static function getInstance(): FabricaDeRepositoriosOracle 
    {
        if (self::$instance === null) 
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getProcesoRepository(): ProcesoRepository 
    {
        return $this->procesoRepo;
    }

    public function getActividadRepository(): ActividadRepository
    {
        return $this->actividadRepo;
    }

    public function getMetodologiaRepository(): MetodologiaRepository {
        return $this->metodologiaRepo;
    }

    public function getModalidadRepository(): ModalidadRepository {
        return $this->modalidadRepo;
    }

    public function getNivelEducativoRepository(): NivelEducativoRepository {
        return $this->nivelEducativoRepo;
    }

    public function getJornadaRepository(): JornadaRepository {
        return $this->jornadaRepo;
    }

    public function getUnidadRegionalRepository(): UnidadRegionalRepository {
        return $this->unidadRegionalRepo;
    }

    public function getProgramaRepository(): ProgramaRepository {
        return $this->programaRepo;
    }

    public function getProgramaContactoRepository(): ProgramaContactoRepository {
        return $this->programaContactoRepo;
    }

    public function getNotificacionRepository(): NotificacionRepository {
        return $this->notificacionRepo;
    }

    public function getProcesoDocumentoRepository(): ProcesoDocumentoRepository {
        return $this->procesoDocumentoRepo;
    }

    public function getEstudianteRepository(): EstudianteRepository {
        return $this->estudianteRepo;
    }  
    
    public function getEnlaceActualizacionRepository(): EnlaceActualizacionRepository {
        return $this->enlaceActualizacionRepo;
    }
}