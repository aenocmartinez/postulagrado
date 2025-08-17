<?php

namespace Src\application\procesos;

use Src\Application\procesos\DTO\EditarProcesoDTO;
use Src\domain\proceso\valueObject\ProcesoId;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class BuscarProcesoUseCase
{
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepository;

    public function __construct(ProcesoRepository $procesoRepo, NivelEducativoRepository $nivelEducativoRepository)
    {
        $this->procesoRepo = $procesoRepo;
        $this->nivelEducativoRepository = $nivelEducativoRepository;
    }

    public function ejecutar(int $id): ResponsePostulaGrado
    {

        $procesoID = ProcesoId::fromInt($id);

        /** @var \Src\admisiones\domain\Proceso $proceso */
        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID->value());
        if (!$proceso->existe()) {        
            return new ResponsePostulaGrado(404, "Proceso no encontrado");
        }

        $editarProcesoDTO = new EditarProcesoDTO(
            $proceso->getId(),
            $proceso->getNombre(),
            $proceso->getNivelEducativoID(),
            $proceso->getEstado(),
            $this->nivelEducativoRepository->Listar()
        );

        return new ResponsePostulaGrado(200, "Proceso encontrado", $editarProcesoDTO);
    }
}