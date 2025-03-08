<?php

namespace Src\shared\di;

use Src\admisiones\dao\mysql\CalendarioDao;
use Src\admisiones\dao\mysql\ProcesoDao;
use Src\admisiones\repositories\CalendarioRepository;
use Src\admisiones\repositories\ProcesoRepository;

class FabricaDeRepositorios 
{
    private static ?FabricaDeRepositorios $instance = null;

    private ProcesoRepository $procesoRepo;
    private CalendarioRepository $calendarioRepo;

    public function __construct()
    {
        $this->procesoRepo = new ProcesoDao();
        $this->calendarioRepo = new CalendarioDao();
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
}