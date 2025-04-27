<?php

namespace Src\Admisiones\UseCase\Notificaciones;

use Src\Shared\Notifications\GestorNotificaciones;
use Src\Shared\Notifications\NotificacionDTO;
use Src\Admisiones\Domain\Proceso;
use Src\Shared\DI\FabricaDeRepositorios;

class InformarActividadesProcesoUseCase
{
    protected $gestorNotificaciones;
    protected $contactoRepo;

    public function __construct()
    {
        $this->gestorNotificaciones = new GestorNotificaciones();
        $this->contactoRepo = FabricaDeRepositorios::getInstance()->getProgramaContactoRepository();
    }

    /**
     * Ejecutar el caso de uso de informar sobre las actividades del proceso.
     *
     * @param Proceso $proceso
     * @param bool $hayCambioCronograma
     * @return bool
     */
    public function ejecutar(Proceso $proceso, bool $hayCambioCronograma): bool
    {
        $contactos = $this->contactoRepo->listar();

        $asunto = $this->crearAsunto($proceso, $hayCambioCronograma);

        $mensaje = $this->crearMensaje($proceso, $hayCambioCronograma);

        $destinatarios = [];
        foreach ($contactos as $contacto) {
            $destinatarios[] = $contacto->getEmail(); 
        }

        $notificacionDTO = new NotificacionDTO($asunto, $mensaje, $destinatarios, ['mailtrap']);

        return $this->gestorNotificaciones->enviarNotificacion($notificacionDTO);
    }

    /**
     * Crear el asunto de la notificación.
     *
     * @param Proceso $proceso
     * @param bool $hayCambioCronograma
     * @return string
     */
    private function crearAsunto(Proceso $proceso, bool $hayCambioCronograma): string
    {
        if ($hayCambioCronograma) {
            return "Actualización cronograma de actividades: " . $proceso->getNombre();
        }

        return "Cronograma de actividades: " . $proceso->getNombre();
    }

    /**
     * Crear el mensaje de la notificación, que incluye la lista de actividades.
     *
     * @param Proceso $proceso
     * @param bool $hayCambioCronograma
     * @return string
     */
    private function crearMensaje(Proceso $proceso, bool $hayCambioCronograma): string
    {
        $actividades = $proceso->getActividades(); 
        $mensaje = $hayCambioCronograma ? "Este es el cronograma actualizado de actividades del proceso de grado:\n\n" : "El cronograma de actividades del proceso de grado incluye las siguientes actividades:\n\n";

        $mensajeHTML = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                        <tr style='background-color: #f2f2f2;'>
                            <th>Descripción</th>
                            <th>Fecha de Inicio</th>
                            <th>Fecha de Fin</th>
                        </tr>";

        foreach ($actividades as $actividad) {
            $mensajeHTML .= "<tr>
                                <td>" . $actividad->getDescripcion() . "</td>
                                <td>" . $actividad->getFechaInicio() . "</td>
                                <td>" . $actividad->getFechaFin() . "</td>
                              </tr>";
        }

        $mensajeHTML .= "</table>";

        return $mensaje . $mensajeHTML; 
    }
}
