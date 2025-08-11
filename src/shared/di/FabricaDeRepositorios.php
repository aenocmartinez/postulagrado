<?php

namespace Src\shared\di;

use Src\infraestructure\dao\oracle\ActividadDao;
use Src\infraestructure\dao\oracle\EnlaceActualizacionDao;
use Src\infraestructure\dao\oracle\EstudianteDao;
use Src\infraestructure\dao\oracle\JornadaDao;
use Src\infraestructure\dao\oracle\MetodologiaDao;
use Src\infraestructure\dao\oracle\ModalidadDao;
use Src\infraestructure\dao\oracle\NivelEducativoDao;
use Src\infraestructure\dao\oracle\NotificacionDao;
use Src\infraestructure\dao\oracle\ProcesoDao;
use Src\infraestructure\dao\oracle\ProcesoDocumentoDao;
use Src\infraestructure\dao\oracle\ProgramaContactoDao;
use Src\infraestructure\dao\oracle\ProgramaDao;
use Src\infraestructure\dao\oracle\UnidadRegionalDao;
use Src\repositories\ActividadRepository;
use Src\repositories\EnlaceActualizacionRepository;
use Src\repositories\EstudianteRepository;
use Src\repositories\JornadaRepository;
use Src\repositories\MetodologiaRepository;
use Src\repositories\ModalidadRepository;
use Src\repositories\NivelEducativoRepository;
use Src\repositories\NotificacionRepository;
use Src\repositories\ProcesoDocumentoRepository;
use Src\repositories\ProcesoRepository;
use Src\repositories\ProgramaContactoRepository;
use Src\repositories\ProgramaRepository;
use Src\repositories\UnidadRegionalRepository;

class FabricaDeRepositorios 
{
    private static ?FabricaDeRepositorios $instance = null;

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
        $this->procesoRepo = new ProcesoDao();
        $this->actividadRepo = new ActividadDao();
        $this->metodologiaRepo = new MetodologiaDao();
        $this->modalidadRepo = new ModalidadDao();
        $this->nivelEducativoRepo = new NivelEducativoDao();
        $this->jornadaRepo = new JornadaDao();
        $this->unidadRegionalRepo = new UnidadRegionalDao();
        $this->programaRepo = new ProgramaDao();
        $this->programaContactoRepo = new ProgramaContactoDao();
        $this->notificacionRepo = new NotificacionDao();
        $this->procesoDocumentoRepo = new ProcesoDocumentoDao();
        $this->estudianteRepo = new EstudianteDao();
        $this->enlaceActualizacionRepo = new EnlaceActualizacionDao();
    }

    public static function getInstance(): FabricaDeRepositorios 
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