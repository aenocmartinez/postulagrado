<?php

namespace Src\usecase\programas;

use Illuminate\Support\Facades\Auth;
use Src\repositories\ProcesoRepository;
use Src\repositories\ProgramaRepository;
use Src\Shared\Notifications\GestorNotificaciones;
use Src\Shared\Notifications\MensajesPersonalizados;
use Src\Shared\Notifications\NotificacionDTO;
use Src\shared\response\ResponsePostulaGrado;

class EnviarEnlaceActualizacionUseCase
{
    protected GestorNotificaciones $gestorNotificaciones;
    private ProgramaRepository $programaRepo;
    private ProcesoRepository $procesoRepo;
    
    public function __construct(ProgramaRepository $programaRepo, ProcesoRepository $procesoRepo) {
        $this->gestorNotificaciones = new GestorNotificaciones();
        $this->programaRepo = $programaRepo;
        $this->procesoRepo = $procesoRepo;
    }

    public function ejecutar(int $procesoID): ResponsePostulaGrado
    {
        /** @var App\Models\User $user */
        $user = Auth::user();
        $programa = $user->programaAcademico();

        $programaProceso = $this->procesoRepo->buscarProgramaPorProceso($procesoID, $programa->getId());

        if (!$programaProceso->existe()) {
            return new ResponsePostulaGrado(404, "Programa no asociado al proceso.");
        }        

        foreach($programa->listarEstudiantesCandidatos($procesoID) as $estudiante) {

            $mensajeHtml = MensajesPersonalizados::generarHtmlEnlaceActualizacion(
                "https://www.pulzo.com",
                $estudiante['detalle']->nombres
            );

            $notificacionDTO = new NotificacionDTO(
                'Actualización de información personal – Proceso de postulación a grado',
                $mensajeHtml,
                [$estudiante['detalle']->email_institucional],
                ['mailtrap']
            );

            $this->gestorNotificaciones->enviarNotificacion($notificacionDTO);            
        }


        return new ResponsePostulaGrado(200, "Enviado");
    }

}