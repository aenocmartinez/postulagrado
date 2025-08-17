<?php

namespace Src\application\usecase\enlaceActualizacion;

use Carbon\Carbon;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class ObtenerDatosFormularioActualizacionUseCase
{
    private EnlaceActualizacionRepository $enlaceRepo;
    private ProgramaRepository $programaRepo;

    public function __construct(EnlaceActualizacionRepository $enlaceRepo, ProgramaRepository $programaRepo)
    {
        $this->enlaceRepo = $enlaceRepo;
        $this->programaRepo = $programaRepo;
    }

    public function ejecutar(string $token): ResponsePostulaGrado
    {
        $enlace = $this->enlaceRepo->buscarPorToken($token);
        if (!$enlace) {
            return new ResponsePostulaGrado(404, 'El enlace no existe.');
        }

        if (strtoupper($enlace->getUsado()) === 'S') {
            return new ResponsePostulaGrado(409, 'Este enlace ya fue utilizado.');
        }
        
        $expira = $enlace->getFechaExpira();
        if ($expira && Carbon::now()->greaterThan(Carbon::parse($expira))) {
            return new ResponsePostulaGrado(410, 'Este enlace ha expirado.');
        }        

        $codigo = $enlace->getCodigoEstudiante();
        $estudiante = $this->programaRepo->obtenerEstudiantePorCodigo($codigo);
        if (!$estudiante) {
            return new ResponsePostulaGrado(404, 'No se encontró información del estudiante.');
        }        

        $data = [
            'token'         => $token,
            'proceso_id'    => $enlace->getProcesoID(),
            'codigo'        => $codigo,
            'estudiante'    => $estudiante,
            'esPostgrado'   => true,
        ];

        return new ResponsePostulaGrado(200, 'OK', $data);        
    }
}