<?php

namespace Src\infrastructure\controller\programa\estudiante;

use Illuminate\Support\Facades\Auth;
use Src\application\programas\estudiante\ActualizacionDatosDTO;
use Src\application\programas\estudiante\ActualizarDatosEstudianteUseCase;
use Src\domain\repositories\EstudianteRepository;
use Src\shared\response\ResponsePostulaGrado;

class GuardarDatosEstudianteController
{
    
    public function __construct(
        private EstudianteRepository $estudianteRepo
    ){}

    public function __invoke(ActualizacionDatosDTO $datos): ResponsePostulaGrado
    {

        /** @var  App\Models\User $user*/
        $user = Auth::user();        
        $datos->programa_id = $user->programaAcademico()->getId();


        return (new ActualizarDatosEstudianteUseCase(
            $this->estudianteRepo
        ))->ejecutar($datos);
    }
}