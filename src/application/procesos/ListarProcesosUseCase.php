<?php

namespace Src\application\procesos;

use Src\Application\procesos\DTO\ProcesoIndexDTO;
use Src\domain\repositories\NivelEducativoRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarProcesosUseCase
{
    private ProcesoRepository $procesoRepo;
    private NivelEducativoRepository $nivelEducativoRepo;

    public function __construct(ProcesoRepository $procesoRepo, NivelEducativoRepository $nivelEducativoRepo)
    {
        $this->procesoRepo = $procesoRepo;
        $this->nivelEducativoRepo = $nivelEducativoRepo;
    }

    public function ejecutar(): ResponsePostulaGrado
    {
        $procesos = $this->procesoRepo->listarProcesos();

        $nivelIds = array_values(array_unique(array_map(
            fn($p) => $p->getNivelEducativoID(),
            $procesos
        )));

        $niveles       = $this->nivelEducativoRepo->Listar(); 
        $idsNecesarios = array_fill_keys($nivelIds, true);
        $nombrePorId   = [];
        foreach ($niveles as $n) {
            $id = $n->getId();
            if (isset($idsNecesarios[$id])) {
                $nombrePorId[$id] = $n->getNombre();
            }
        }

        $items = [];
        foreach ($procesos as $p) {
            $nivelId = $p->getNivelEducativoID();
            $items[] = new ProcesoIndexDTO(
                id: $p->getId() ?? 0,
                nombre: $p->getNombre(),
                nivelEducativoNombre: $nombrePorId[$nivelId] ?? '—',
                estado: $p->getEstado()
            );
        }

        return new ResponsePostulaGrado(200, 'Procesos obtenidos exitosamente.', $items);
    }

}