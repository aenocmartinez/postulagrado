<?php

namespace Src\infrastructure\controller\proceso\actividad;

use Src\application\procesos\actividades\ListarActividadesUseCase;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarActividadController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private ActividadRepository $actividadRepository,
        private NivelEducativoRepository $nivelEducativoRepository,
    ){}

    public function __invoke(int $procesoID): ResponsePostulaGrado
    {
        try
        {
            $procesoID = filter_var($procesoID, FILTER_VALIDATE_INT);
            if ($procesoID === false) {
                return new ResponsePostulaGrado(500, 'ID de proceso inválido.');
            }

            return (new ListarActividadesUseCase(
                    $this->procesoRepository, 
                    $this->actividadRepository, 
                    $this->nivelEducativoRepository)
                )->ejecutar($procesoID);             
        }
        catch (\Throwable $e)
        {
            return new ResponsePostulaGrado(500, $e->getMessage());
        }
        
    }
}