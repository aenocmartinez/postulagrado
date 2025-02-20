<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearProceso extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Permite la validación
    }

    /**
     * Prepara los datos antes de validarlos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'nombre' => trim($this->nombre),
            'nivelEducativo' => trim($this->nivelEducativo),
        ]);
    }

    /**
     * Reglas de validación para la creación de un proceso.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:procesos,nombre',
            'nivelEducativo' => 'required|in:Pregrado,Postgrado',
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del proceso es obligatorio.',
            'nombre.string' => 'El nombre del proceso debe ser un texto válido.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un proceso con este nombre.',
            'nivelEducativo.required' => 'El nivel educativo es obligatorio.',
            'nivelEducativo.in' => 'El nivel educativo debe ser Pregrado o Postgrado.',
        ];
    }
}
