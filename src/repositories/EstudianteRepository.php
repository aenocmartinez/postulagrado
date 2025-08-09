<?php

namespace Src\repositories;


interface EstudianteRepository {
    // Está duplicado en programaDao
    public function buscarEstudiantePorCodigo(string|array $codigosEstudiante): array;
}