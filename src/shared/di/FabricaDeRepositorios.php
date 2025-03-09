<?php

namespace Src\shared\di;

use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\dao\mysql\JornadaDao;
use Src\admisiones\dao\mysql\MetodologiaDao;
use Src\admisiones\dao\mysql\ModalidadDao;
use Src\admisiones\dao\mysql\ModalidadDaoDao;
use Src\admisiones\dao\mysql\NivelEducativoDao;
use Src\admisiones\dao\mysql\ProcesoDao;
use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\JornadaRepository;
use Src\admisiones\repositories\MetodologiaRepository;
use Src\admisiones\repositories\ModalidadRepository;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\admisiones\repositories\ProcesoRepository;

class FabricaDeRepositorios 
{
    private static ?FabricaDeRepositorios $instance = null;

    private ProcesoRepository $procesoRepo;
    private CalendarioRepository $calendarioRepo;
    private MetodologiaRepository $metodologiaRepo;
    private ModalidadRepository $modalidadRepo;
    private NivelEducativoRepository $nivelEducativoRepo;
    private JornadaRepository $jornadaRepo;

    public function __construct()
    {
        $this->procesoRepo = new ProcesoDao();
        $this->calendarioRepo = new CalendarioDao();
        $this->metodologiaRepo = new MetodologiaDao();
        $this->modalidadRepo = new ModalidadDao();
        $this->nivelEducativoRepo = new NivelEducativoDao();
        $this->jornadaRepo = new JornadaDao();
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
}