<?php

namespace Src\admisiones\usecase\actividades;

use Illuminate\Support\Facades\Cache;
use Src\admisiones\domain\Actividad;
use Src\admisiones\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class AgregarActividadesMasivoUseCase
{
    private ProcesoRepository $procesoRepo;

    public function __construct(ProcesoRepository $procesoRepo)
    {
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(int $procesoID, array $actividades): ResponsePostulaGrado
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado.");
        }
    
        if (empty($actividades)) 
        {
            return new ResponsePostulaGrado(400, "No se recibieron actividades para guardar.");
        }
      
        $idsExistentes = array_map(function($actividad) {
            return $actividad->getId();
        }, $proceso->getActividades());
    

        $idsRecibidos = array_filter(array_column($actividades, 'id'), fn($id) => $id > 0);
    
        $idsEliminados = array_diff($idsExistentes, $idsRecibidos);
    
        foreach ($idsEliminados as $idEliminar) {
            $actividad = new Actividad();
            $actividad->setId($idEliminar);
            $proceso->quitarActividad($actividad); 
        }
    
        foreach ($actividades as $item) 
        {
            $actividad = new Actividad();
            $actividad->setId($item['id']);
            $actividad->setDescripcion($item['descripcion']);
            $actividad->setFechaInicio($item['fecha_inicio']);
            $actividad->setFechaFin($item['fecha_fin']);
    
            $exito = $proceso->agregarActividad($actividad);
    
            if (!$exito) 
            {
                return new ResponsePostulaGrado(500, "Se produjo un error al guardar las actividades. Intente nuevamente.");
            }
        }
        
        Cache::forget('actividades_listado_proceso_' . $procesoID);
        Cache::forget('proceso_'.$procesoID);
    
        return new ResponsePostulaGrado(201, "La información se ha guardado con éxito.");
    }
    
}
