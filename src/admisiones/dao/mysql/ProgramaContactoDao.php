<?php

namespace Src\admisiones\dao\mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Src\admisiones\domain\ProgramaContacto;
use Src\admisiones\repositories\ProgramaContactoRepository;
use Src\shared\di\FabricaDeRepositorios;

class ProgramaContactoDao extends Model implements ProgramaContactoRepository {

    protected $table = 'programa_contactos';
    protected $fillable = ['nombre', 'telefono', 'email', 'observacion', 'programa_id'];

    /**
     * Listar los contactos filtrados por un criterio de búsqueda opcional.
     */
    public function listar(string $criterio = ""): array {
        $contactos = [];
    
        try {
            $query = self::query()
                ->leftJoin('programas', 'programa_contactos.programa_id', '=', 'programas.id')
                ->select(
                    'programa_contactos.*',
                    'programas.nombre as programa_nombre'
                );
    
            if (!empty($criterio)) {
                $query->where(function ($q) use ($criterio) {
                    $q->where('programa_contactos.nombre', 'LIKE', "%{$criterio}%")
                      ->orWhere('programa_contactos.email', 'LIKE', "%{$criterio}%")
                      ->orWhere('programa_contactos.telefono', 'LIKE', "%{$criterio}%")
                      ->orWhere('programas.nombre', 'LIKE', "%{$criterio}%");
                });
            }

            $query->orderBy('programa_contactos.nombre');
    
            $registros = $query->get();
    
            foreach ($registros as $registro) {
                $contacto = new ProgramaContacto(
                    FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
                );
    
                $contacto->setId($registro->id);
                $contacto->setNombre($registro->nombre);
                $contacto->setTelefono($registro->telefono);
                $contacto->setEmail($registro->email);
                $contacto->setObservacion($registro->observacion);
    
                if (!is_null($registro->programa_id)) {
                    $programaRepo = FabricaDeRepositorios::getInstance()->getProgramaRepository();
                    $programa = $programaRepo->buscarPorID($registro->programa_id);
                    $contacto->setPrograma($programa);
                }
    
                $contactos[] = $contacto;
            }
        } catch (\Exception $e) {
            Log::error("Error al listar contactos: " . $e->getMessage());
        }
    
        return $contactos;
    }    

    /**
     * Buscar un contacto por su ID.
     */
    public function buscarPorID(int $id): ProgramaContacto {
        try {
            $registro = self::find($id);

            if ($registro) {
                $contacto = new ProgramaContacto(
                    FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
                );

                $contacto->setId($registro->id);
                $contacto->setNombre($registro->nombre);
                $contacto->setTelefono($registro->telefono);
                $contacto->setEmail($registro->email);
                $contacto->setObservacion($registro->observacion);

                if (!is_null($registro->programa_id)) {
                    $programaRepo = FabricaDeRepositorios::getInstance()->getProgramaRepository();
                    $programa = $programaRepo->buscarPorID($registro->programa_id);
                    $contacto->setPrograma($programa);
                }

                return $contacto;
            }
        } catch (\Exception $e) {
            Log::error("Error al buscar contacto por ID {$id}: " . $e->getMessage());
        }

        return new ProgramaContacto(
            FabricaDeRepositorios::getInstance()->getProgramaContactoRepository()
        );
    }

    /**
     * Crear un nuevo contacto en la base de datos.
     */
    public function crear(ProgramaContacto $contacto): bool {
        try {
            $registro = self::create([
                'nombre' => $contacto->getNombre(),
                'telefono' => $contacto->getTelefono(),
                'email' => $contacto->getEmail(),
                'observacion' => $contacto->getObservacion(),
                'programa_id' => $contacto->getPrograma()?->getId()
            ]);

            if ($registro instanceof self) {
                $contacto->setId($registro->id);
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Error al crear contacto: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Actualizar un contacto en la base de datos.
     */
    public function actualizar(ProgramaContacto $contacto): bool {
        try {
            $filasAfectadas = self::where('id', $contacto->getId())
                ->update([
                    'nombre' => $contacto->getNombre(),
                    'telefono' => $contacto->getTelefono(),
                    'email' => $contacto->getEmail(),
                    'observacion' => $contacto->getObservacion(),
                    'programa_id' => $contacto->getPrograma()?->getId()
                ]);

            return $filasAfectadas > 0;
        } catch (\Exception $e) {
            Log::error("Error al actualizar contacto ID {$contacto->getId()}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Eliminar un contacto por su ID.
     */
    public function eliminar(int $programaContactoID): bool {
        try {
            $eliminados = self::where('id', $programaContactoID)->delete();

            if ($eliminados === 0) {
                Log::warning("Intento de eliminar un contacto que no existe: ID {$programaContactoID}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error al eliminar contacto ID {$programaContactoID}: " . $e->getMessage());
        }

        return false;
    }
}
