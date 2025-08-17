<?php

namespace Src\application\nivelEducativo;

use Src\domain\repositories\NivelEducativoRepository;
use Src\shared\response\ResponsePostulaGrado;

class ListarNivelEducativoUseCase
{
    private NivelEducativoRepository $nivelEducativoRepo;

    public function __construct(NivelEducativoRepository $nivelEducativoRepo)
    {
        $this->nivelEducativoRepo = $nivelEducativoRepo;
    }

    public function ejecutar(): array
    {
        return $this->nivelEducativoRepo->Listar();
    }
}