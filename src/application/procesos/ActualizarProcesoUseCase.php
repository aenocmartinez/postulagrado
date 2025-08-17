<?php

namespace Src\application\procesos;

use App\Http\Requests\ActualizarProceso;
use Illuminate\Support\Facades\Log;
use Src\Application\procesos\DTO\EditarProcesoDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\dto\proceso\ProcesoDTO;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarProcesoUseCase
{
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelRepo;

    public function __construct(ProcesoRepository $procesoRepo, NivelEducativoRepository $nivelRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->nivelRepo = $nivelRepo;
    }

    public function ejecutar(EditarProcesoDTO $editarProcesoDTO): ResponsePostulaGrado 
    {
        $proceso = $this->procesoRepo->buscarProcesoPorId($editarProcesoDTO->id);
        if (!$proceso->existe()) 
        {
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $nivelEducativo = $this->nivelRepo->BuscarPorID($editarProcesoDTO->nivelEducativoID);
        if (!$nivelEducativo->existe()) 
        {
            return new ResponsePostulaGrado(404, "Nivel educativo no encontrado.");
        }        

        $proceso->setNombre($editarProcesoDTO->nombre);
        $proceso->setNivelEducativoID($editarProcesoDTO->nivelEducativoID);
                
        $exito = $this->procesoRepo->actualizarProceso($proceso);
        if (!$exito) 
        {
            return new ResponsePostulaGrado(500, "Se ha producido un error en el sistema. Por favor, inténtelo de nuevo más tarde.");
        }

        return new ResponsePostulaGrado(200, "Proceso actualizado exitosamente.");
    }
}