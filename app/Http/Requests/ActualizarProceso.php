<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarProceso extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos antes de validarlos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
            'nombre' => trim($this->nombre),
            'nivelEducativo' => trim($this->nivelEducativo),
        ]);
    }

    /**
     * Reglas de validación para la actualización de un proceso.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'nombre' => 'required|string|max:255',
            'nivelEducativo' => 'required',
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'El ID del proceso es obligatorio.',
            'id.integer' => 'El ID del proceso debe ser un número entero válido.',

            'nombre.required' => 'El nombre del proceso es obligatorio.',
            'nombre.string' => 'El nombre del proceso debe ser un texto válido.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',

            'nivelEducativo.required' => 'El nivel educativo es obligatorio.',
        ];
    }
}
