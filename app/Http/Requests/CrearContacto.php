<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearContacto extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación que se aplicarán a la solicitud.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:40',
            'email' => 'required|email|max:255',
            'programa_id' => 'required|integer|exists:programas,id',
            'observacion' => 'nullable|string|max:2000', // Permite nulo o texto
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.max' => 'El teléfono no puede superar los 40 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'programa_id.required' => 'Debe seleccionar un programa.',
            'programa_id.integer' => 'El programa debe ser un número entero.',
            'programa_id.exists' => 'El programa seleccionado no es válido.',
            'observacion.max' => 'La observación no puede superar los 2000 caracteres.',
        ];
    }

    /**
     * Modificar los datos ANTES de la validación.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'observacion' => $this->observacion ?? '', // Si es null, convertir a ""
        ]);
    }
}
