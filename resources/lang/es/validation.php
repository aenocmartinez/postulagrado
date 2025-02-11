<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'Debe ser una dirección de correo válida.',
    'string' => 'El campo :attribute debe ser un texto.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'auth' => [
        'failed' => 'Las credenciales ingresadas no son correctas.',
        'throttle' => 'Demasiados intentos. Por favor, intenta de nuevo en :seconds segundos.',
    ],
];
