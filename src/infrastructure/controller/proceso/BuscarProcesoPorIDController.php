<?php 

namespace Src\infrastructure\controller\proceso;

use Src\application\procesos\BuscarProcesoUseCase;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarProcesoPorIDController
{
    public function __construct(
        private ProcesoRepository $procesoRepository,
        private NivelEducativoRepository $nivelEducativoRepository,
        ){}

    public function __invoke(string|int $procesoID): ResponsePostulaGrado
    {
        try 
        {
            $procesoID = filter_var($procesoID, FILTER_VALIDATE_INT);
            if ($procesoID === false) {
                return new ResponsePostulaGrado(500, 'ID de proceso inválido.');
            }

            return (new BuscarProcesoUseCase($this->procesoRepository, $this->nivelEducativoRepository))->ejecutar($procesoID);
        } 
        catch (\Throwable $e) {
            return new ResponsePostulaGrado(500, 'Ha ocurrido un error interno.');
        }
    }
}