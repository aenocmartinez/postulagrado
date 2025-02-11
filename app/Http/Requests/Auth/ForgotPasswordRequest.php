<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $value,
                ]);

                if (!$response->json('success')) {
                    $fail('La verificación de reCAPTCHA falló. Inténtalo de nuevo.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Captcha es obligatorio.',
        ];
    }    
}
