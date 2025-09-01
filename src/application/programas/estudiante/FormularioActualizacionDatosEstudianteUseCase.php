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
            return new ResponsePostulaGrado(404, 'El formulario al que intenta acceder no es válido. Verifique la dirección o solicite nuevamente el acceso en la Universidad.');
        }

        if (strtoupper($enlace->getUsado()) === 'S') {
            return new ResponsePostulaGrado(409, 'Este formulario ya fue diligenciado anteriormente. Si necesita realizar una nueva actualización, solicite un nuevo acceso en la Universidad.');
        }
        
        $expira = $enlace->getFechaExpira();
        if ($expira && Carbon::now()->greaterThan(Carbon::parse($expira))) {
            return new ResponsePostulaGrado(410, 'El formulario ya no está disponible porque ha superado su tiempo de vigencia. Por favor, solicite un nuevo acceso para continuar con el proceso.');
        }        

        $codigo = $enlace->getCodigoEstudiante();
        $estudiante = $this->programaRepo->obtenerEstudiantePorCodigo($codigo);
        if (!$estudiante) {
            return new ResponsePostulaGrado(404, 'No fue posible localizar la información del estudiante asociada a este formulario. Por favor, confirme sus datos o comuníquese con Soporte Académico.');
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