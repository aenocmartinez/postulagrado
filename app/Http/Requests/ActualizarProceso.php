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
        return true; // Permitir la validación
    }

    /**
     * Prepara los datos antes de validarlos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'), // Obtiene el ID desde la URL
            'nombre' => trim($this->nombre),
            'nivelEducativo' => trim($this->nivelEducativo),
            'estado' => trim($this->estado),
        ]);
    }

    /**
     * Reglas de validación para la actualización de un proceso.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:procesos,id',
            'nombre' => 'required|string|max:255|unique:procesos,nombre,' . $this->id . ',id,nivel_educativo,' . $this->nivelEducativo,
            'nivelEducativo' => 'required|in:Pregrado,Postgrado',
            'estado' => 'required|in:Abierto,Cerrado',
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
            'id.exists' => 'El proceso seleccionado no existe.',

            'nombre.required' => 'El nombre del proceso es obligatorio.',
            'nombre.string' => 'El nombre del proceso debe ser un texto válido.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un proceso con este nombre y nivel educativo.',

            'nivelEducativo.required' => 'El nivel educativo es obligatorio.',
            'nivelEducativo.in' => 'El nivel educativo debe ser Pregrado o Postgrado.',

            'estado.required' => 'El estado del proceso es obligatorio.',
            'estado.in' => 'El estado debe ser Abierto o Cerrado.',
        ];
    }
}
