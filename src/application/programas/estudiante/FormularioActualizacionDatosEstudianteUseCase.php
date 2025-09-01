<?php

namespace Src\application\programas\estudiante;

use Carbon\Carbon;
use Src\domain\repositories\EnlaceActualizacionRepository;
use Src\domain\repositories\ProgramaRepository;
use Src\shared\response\ResponsePostulaGrado;

class FormularioActualizacionDatosEstudianteUseCase
{

    public function __construct(
        private EnlaceActualizacionRepository $enlaceRepo, 
        private ProgramaRepository $programaRepo
    ){}

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
            'enlace_id'     => $enlace->getId(),
            'token'         => $token,
            'proceso_id'    => $enlace->getProcesoID(),
            'codigo'        => $codigo,
            'estudiante'    => $estudiante,
            'esPostgrado'   => true,
        ];

        // dd($data);

        return new ResponsePostulaGrado(200, 'OK', $data);        
    }
}