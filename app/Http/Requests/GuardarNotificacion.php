<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarNotificacion extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'asunto'        => 'required|string|max:255',
            'mensaje'       => 'required|string',
            'destinatarios' => 'required|array|min:1',
            'fecha_envio'   => 'required|date',
            'canal'         => 'required|string|max:50',
            'proceso_id'    => 'required|integer',
        ];
    }

    /**
     * Mensajes personalizados de error.
     */
    public function messages(): array
    {
        return [
            'asunto.required' => 'El asunto de la notificación es obligatorio.',
            'mensaje.required' => 'El mensaje de la notificación es obligatorio.',
            'destinatarios.required' => 'Debes seleccionar al menos un destinatario.',
            'destinatarios.array' => 'Los destinatarios deben ser enviados en formato válido.',
            'fecha_envio.required' => 'La fecha de envío es obligatoria.',
            'fecha_envio.date' => 'La fecha de envío debe ser una fecha válida.',
            'canal.required' => 'El canal de notificación es obligatorio.',
        ];
    }
}
