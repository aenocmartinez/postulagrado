<?php

namespace Src\shared\di;

// use Src\admisiones\infraestructure\dao\mysql\CalendarioDao;
// use Src\admisiones\infraestructure\dao\mysql\JornadaDao;
// use Src\admisiones\infraestructure\dao\mysql\MetodologiaDao;
// use Src\admisiones\infraestructure\dao\mysql\ModalidadDao;
// use Src\admisiones\infraestructure\dao\mysql\NivelEducativoDao;
// use Src\admisiones\infraestructure\dao\mysql\ProcesoDao;
// use Src\admisiones\infraestructure\dao\mysql\ProgramaContactoDao;
// use Src\admisiones\infraestructure\dao\mysql\ProgramaDao;
// use Src\admisiones\infraestructure\dao\mysql\UnidadRegionalDao;

use Src\admisiones\infraestructure\dao\mysql\CalendarioDao;
use Src\admisiones\infraestructure\dao\oracle\JornadaDao;
use Src\admisiones\infraestructure\dao\oracle\MetodologiaDao;
use Src\admisiones\infraestructure\dao\oracle\ModalidadDao;
use Src\admisiones\infraestructure\dao\oracle\NivelEducativoDao;
use Src\admisiones\infraestructure\dao\oracle\ProcesoDao;
use Src\admisiones\infraestructure\dao\mysql\ProgramaContactoDao;
use Src\admisiones\infraestructure\dao\oracle\ProgramaDao;
use Src\admisiones\infraestructure\dao\oracle\UnidadRegionalDao;
use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\JornadaRepository;
use Src\admisiones\repositories\MetodologiaRepository;
use Src\admisiones\repositories\ModalidadRepository;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\admisiones\repositories\ProgramaContactoRepository;
use Src\admisiones\repositories\ProgramaRepository;
use Src\admisiones\repositories\UnidadRegionalRepository;

class FabricaDeRepositorios 
{
    private static ?FabricaDeRepositorios $instance = null;

    private ProcesoRepository $procesoRepo;
    private CalendarioRepository $calendarioRepo;
    private MetodologiaRepository $metodologiaRepo;
    private ModalidadRepository $modalidadRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private JornadaRepository $jornadaRepo;
    private UnidadRegionalRepository $unidadRegionalRepo;
    private ProgramaRepository $programaRepo;
    private ProgramaContactoRepository $programaContactoRepo;

    public function __construct()
    {
        $this->procesoRepo = new ProcesoDao();
        $this->calendarioRepo = new CalendarioDao();
        $this->metodologiaRepo = new MetodologiaDao();
        $this->modalidadRepo = new ModalidadDao();
        $this->nivelEducativoRepo = new NivelEducativoDao();
        $this->jornadaRepo = new JornadaDao();
        $this->unidadRegionalRepo = new UnidadRegionalDao();
        $this->programaRepo = new ProgramaDao();
        $this->programaContactoRepo = new ProgramaContactoDao();
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

    public function getCalendarioRepository(): CalendarioRepository
    {
        return $this->calendarioRepo;
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
}