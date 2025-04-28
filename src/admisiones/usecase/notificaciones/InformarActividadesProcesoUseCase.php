<?php

namespace Src\Admisiones\UseCase\Notificaciones;

use Src\Shared\Notifications\NotificacionDTO;
use Src\Admisiones\Repositories\ProgramaContactoRepository;
use Src\Shared\Notifications\GestorNotificaciones;
use Src\Shared\DI\FabricaDeRepositorios;
use Carbon\Carbon;
use Src\admisiones\domain\Proceso;

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

        // Crear asunto
        $asunto = $this->crearAsunto($proceso, $hayCambioCronograma);

        // Preparar destinatarios y nombres
        $destinatarios = [];
        $nombresDestinatarios = [];
        
        foreach ($contactos as $contacto) {
            // Crear el mensaje para cada destinatario
            $mensaje = $this->crearMensaje($proceso, $hayCambioCronograma, $contacto);

            // Crear el DTO con los datos del mensaje personalizado para cada destinatario
            $notificacionDTO = new NotificacionDTO(
                $asunto,
                $mensaje,
                [$contacto->getEmail()],  // Enviar solo a un destinatario
                ['mailtrap'] // Canal de notificación
            );

            // Enviar la notificación
            $this->gestorNotificaciones->enviarNotificacion($notificacionDTO);
        }

        return true;
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
            return "Actualización del cronograma de actividades del proceso de grado: " . $proceso->getNombre();
        }

        return "Cronograma de actividades del proceso de grado: " . $proceso->getNombre();
    }

    /**
     * Crear el mensaje de la notificación, que incluye la lista de actividades.
     *
     * @param Proceso $proceso
     * @param bool $hayCambioCronograma
     * @param $contacto
     * @return string
     */
    private function crearMensaje(Proceso $proceso, bool $hayCambioCronograma, $contacto): string
    {
        $actividades = $proceso->getActividades(); 

        // Obtener el nombre del programa del contacto
        $nombrePrograma = $contacto->getPrograma()->getNombre();

        // Generamos el encabezado con el saludo genérico y personalizamos con el nombre del contacto
        $mensaje = $hayCambioCronograma
            ? "Estimado(a) " . $contacto->getNombre() . " del programa {$nombrePrograma},<br><br>A continuación se brinda la información actualizada sobre el cronograma de actividades del proceso de grado:<br><br>"
            : "Estimado(a) " . $contacto->getNombre() . " del programa {$nombrePrograma},<br><br>A continuación se presenta el cronograma de actividades del proceso de grado:<br><br>";

        // Mensaje HTML con la tabla de actividades
        $mensajeHTML = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                        <tr style='background-color: #f2f2f2;'>
                            <th style='padding: 8px;'>Descripción</th>
                            <th style='padding: 8px;'>Fecha de Inicio</th>
                            <th style='padding: 8px;'>Fecha de Fin</th>
                        </tr>";

        foreach ($actividades as $actividad) {
            // Formateamos las fechas para que solo aparezca la fecha (sin hora)
            $fechaInicio = Carbon::parse($actividad->getFechaInicio())->format('Y-m-d');
            $fechaFin = Carbon::parse($actividad->getFechaFin())->format('Y-m-d');
            
            $mensajeHTML .= "<tr>
                                <td style='padding: 8px;'>" . $actividad->getDescripcion() . "</td>
                                <td style='padding: 8px;'>" . $fechaInicio . "</td>
                                <td style='padding: 8px;'>" . $fechaFin . "</td>
                              </tr>";
        }

        $mensajeHTML .= "</table>";

        // Agregar la firma institucional al final
        $mensaje .= $mensajeHTML . "<br><br>Atentamente,<br><br><strong>Área de Admisiones</strong><br><strong>Universidad Colegio Mayor de Cundinamarca</strong>";

        return $mensaje;
    }
}
