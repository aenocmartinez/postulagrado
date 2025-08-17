<?php

namespace Src\application\procesos\actividades;

use Src\application\procesos\actividades\DTO\ProcesoActividadDTO;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarActividadesUseCase
{

    public function __construct(
        private ProcesoRepository $procesoRepo, 
        private ActividadRepository $actividadRepo,
        private NivelEducativoRepository $nivelEducativoRepo,
        ) {}

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe())
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $nivelEducativo = $this->nivelEducativoRepo->BuscarPorID($proceso->getNivelEducativoID());

        $procesoActividad = new ProcesoActividadDTO();
        $procesoActividad->nombreProceso = $proceso->getNombre();
        $procesoActividad->estadoProceso = $proceso->getEstado();
        $procesoActividad->nombreNivelEducativo = $nivelEducativo->getNombre();
        $procesoActividad->procesoID = $proceso->getId();

        foreach ($this->actividadRepo->listarActividades($procesoID) as $actividad) {
            $procesoActividad->actividades[] = [
                'id' => $actividad->id()->value(),
                'nombre' => $actividad->descripcion()->value(),
                'fechaInicio' => $actividad->inicio()->format('Y-m-d'),
                'fechaFin' => $actividad->fin()->format('Y-m-d'),
            ];
        }

        return new ResponsePostulaGrado(200, "Actividades del proceso {$procesoID}", $procesoActividad);
    }
}