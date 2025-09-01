<?php

namespace Src\application\programas\estudiante;

use Carbon\Carbon;
use DateTimeInterface;
use Src\domain\repositories\EstudianteRepository;
use Src\shared\response\ResponsePostulaGrado;

class ActualizarDatosEstudianteUseCase
{
    public function __construct(
        private EstudianteRepository $estudianteRepo
    ) {}

    public function ejecutar(ActualizacionDatosDTO $datos): ResponsePostulaGrado
    {
        $enlace = $this->estudianteRepo->buscarEnlacePorID($datos->enlace_id);
        if (!$enlace) {
            return new ResponsePostulaGrado(404, 'El enlace no existe o no es válido.');
        }

        if (($enlace->acen_usado ?? 'N') === 'S') {
            return new ResponsePostulaGrado(409, 'El enlace ya fue utilizado.');
        }

        if (!empty($enlace->acen_fechaexpira)) {
            $expira = $this->aCarbon($enlace->acen_fechaexpira);
            if ($expira->isPast()) {
                return new ResponsePostulaGrado(410, 'El enlace ha expirado.');
            }
        }

        $ok = $this->estudianteRepo->guardarDatosActualizados($datos);
        if (!$ok) {
            return new ResponsePostulaGrado(500, 'No fue posible guardar la actualización de datos.');
        }

        // 4) OK
        return new ResponsePostulaGrado(
            200,
            'Datos actualizados correctamente.',
            [
                'enlace_id'  => $datos->enlace_id,
                'proceso_id' => $datos->proceso_id,
                'codigo'     => $datos->codigo,
            ]
        );
    }

    private function aCarbon(mixed $valor): Carbon
    {
        if ($valor instanceof DateTimeInterface) {
            return Carbon::instance($valor);
        }
        return Carbon::parse((string) $valor);
    }
}
