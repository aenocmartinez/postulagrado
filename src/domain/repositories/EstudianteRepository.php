<?php

namespace Src\domain\repositories;

use Src\application\programas\estudiante\ActualizacionDatosDTO;

interface EstudianteRepository {
    // Está duplicado en programaDao
    public function buscarEstudiantePorCodigo(string|array $codigosEstudiante): array;
    public function findPpesId(int $procesoId, int $programaId, string $codigo): ?int;
    public function guardarDatosActualizados(ActualizacionDatosDTO $datos): bool;
    public function buscarEnlacePorID(int $enlaceID): ?object;
}