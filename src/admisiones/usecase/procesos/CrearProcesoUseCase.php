<?php

namespace Src\admisiones\usecase\procesos;

use Src\admisiones\dto\proceso\ProcesoDTO;
use Src\admisiones\repositories\NivelEducativoRepository;
use Src\admisiones\repositories\ProcesoRepository;
use Src\admisiones\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearProcesoUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo, 
        private ProgramaRepository $programaRepo,
        private NivelEducativoRepository $nivelRepo,
    ) {
        $this->procesoRepo = $procesoRepo;    
        $this->programaRepo = $programaRepo;
        $this->nivelRepo = $nivelRepo;
    }

    public function ejecutar(ProcesoDTO $procesoDTO): ResponsePostulaGrado
    {
        $nivelEducativo = $this->nivelRepo->BuscarPorID($procesoDTO->getNivelEducativo()->getId());
        if (!$nivelEducativo->existe()) 
        {
            return new ResponsePostulaGrado(404, "Nivel educativo no encontrado.");
        }

        $proceso = $this->procesoRepo->buscarProcesoPorNombreYNivelEducativo($procesoDTO->getNombre(), $nivelEducativo);
        if ($proceso->existe()) 
        {
            return new ResponsePostulaGrado(409, "El nombre del proceso ya está en uso. Por favor, elige un nombre diferente.");
        }

        $proceso->setNombre($procesoDTO->getNombre());
        $proceso->setNivelEducativo($nivelEducativo);
        $proceso->setEstado('ABIERTO');

        $exito = $proceso->crear();
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");   
        }

        // $programas = $this->programaRepo->buscarProgramasPorNivelEducativo($proceso->getNivelEducativo()->getNombre());

        // foreach($programas as $programa) {

        //     $proceso->agregarPrograma($programa);
        // }

        return new ResponsePostulaGrado(201, "El proceso se ha creado exitosamente.");
    }
}