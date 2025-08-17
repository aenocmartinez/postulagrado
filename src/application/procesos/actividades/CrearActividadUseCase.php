<?php

namespace Src\application\procesos\actividades;

use Illuminate\Support\Facades\Cache;
use Src\domain\proceso\actividad\Actividad;
use Src\domain\proceso\actividad\valueObject\ActividadId;
use Src\domain\proceso\actividad\valueObject\DescripcionActividad;
use Src\domain\proceso\actividad\valueObject\RangoFechasActividad;
use Src\domain\repositories\ActividadRepository;
use Src\domain\repositories\ProcesoRepository;
use Src\shared\response\ResponsePostulaGrado;
use Src\UseCase\Notificaciones\InformarActividadesProcesoUseCase;

class CrearActividadUseCase
{
    public function __construct(
        private ProcesoRepository $procesoRepo,
        private ActividadRepository $actividadRepo
    ) {}

    public function ejecutar(int $procesoID, array $actividades): ResponsePostulaGrado
    {

        $cambioContenidoActividad = false;

        $proceso = $this->procesoRepo->buscarProcesoPorId($procesoID);
        if (!$proceso->existe()) {
            return new ResponsePostulaGrado(404, "Proceso no encontrado.");
        }

        $idsExistentes = array_values(array_filter(
            array_map(
                fn(Actividad $a) => (int) ($a->id()->value() ?? 0),
                $this->actividadRepo->listarActividades($proceso->getId())
            ),
            fn (int $id) => $id > 0
        ));

        $idsRecibidos = array_values(array_unique(array_map(
            'intval',
            array_filter(array_column($actividades, 'id'), fn ($id) => (int)$id > 0)
        )));

        $idsActividadesEliminar = array_values(array_diff($idsExistentes, $idsRecibidos));
        foreach ($idsActividadesEliminar as $actividadID) {
            $this->actividadRepo->eliminarActividad((int) $actividadID);
        }        

        foreach ($actividades as $item) {
            $actividad = new Actividad(
                new ActividadId($item['id']),
                new DescripcionActividad($item['descripcion']),
                new RangoFechasActividad($item['fecha_inicio'], $item['fecha_fin'])
            );

            if ($actividad->existe()) {
                $this->actividadRepo->actualizarActividad($actividad);
            } else  {
                $this->actividadRepo->agregarActividad($proceso->getId(), $actividad);
            }
        }

        Cache::forget('actividades_listado_proceso_' . $procesoID);
        Cache::forget('proceso_'.$procesoID);

        return new ResponsePostulaGrado(201, "La información se ha guardado con éxito.");
    }

}
