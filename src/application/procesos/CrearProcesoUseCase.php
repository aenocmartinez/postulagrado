<?php

namespace Src\application\procesos;

use Src\application\procesos\DTO\CrearProcesoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class CrearProcesoUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private NivelEducativoRepository $nivelEducativoRepo,
        private ProgramaRepository $programaRepo
    ){}

    public function ejecutar(CrearProcesoDTO $crearProcesoDTO): ResponsePostulaGrado
    {
        $nivelEducativo = $this->nivelEducativoRepo->BuscarPorID($crearProcesoDTO->getNivelEducativoId());        
        if (!$nivelEducativo->existe()) {
            return new ResponsePostulaGrado(404, "Nivel educativo no encontrado.");
        }

        $proceso = $this->procesoRepo->buscarProcesoPorNombreYNivelEducativo($crearProcesoDTO->getNombre(), $nivelEducativo);
        if ($proceso->existe()) 
        {
            return new ResponsePostulaGrado(409, "El nombre del proceso ya está en uso. Por favor, elige un nombre diferente.");
        }        
        
        $proceso->setNombre($crearProcesoDTO->getNombre());
        $proceso->setNivelEducativoID($crearProcesoDTO->getNivelEducativoId());
        $proceso->setEstado('ABIERTO');

        $exito = $this->procesoRepo->crearProceso($proceso);
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");   
        }      
        
        $programas = $this->programaRepo->buscarProgramasPorNivelEducativo($crearProcesoDTO->getNivelEducativoId());
        foreach($programas as $programa) {
            $this->procesoRepo->agregarPrograma($proceso->getId(), $programa->getId());
        }

        return new ResponsePostulaGrado(201, "Proceso creado exitosamente.");
    }
}