<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateEstudianteDatosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza una cadena para comparaciones robustas:
     * - sin acentos
     * - minúsculas
     * - sin espacios, guiones ni signos
     */
    private function normalize(string $value): string
    {
        // Requiere la extensión intl (Str::ascii) o se puede reemplazar con iconv
        return (string) Str::of($value)
            ->ascii()                      // quita acentos
            ->lower()                      // minúsculas
            ->replaceMatches('/[^a-z0-9]/', ''); // quita separadores
    }

    public function rules(): array
    {
        // Target para UCMC (aceptamos variantes con y sin guión)
        $ucmcTargets = [
            $this->normalize('Universidad Colegio Mayor de Cundinamarca'),
            $this->normalize('Universidad-Colegio Mayor de Cundinamarca'),
        ];

        return [
            // Ocultos de control
            'enlace_id' => [
                'required', 'integer',
                // Si usas conexión Oracle nombrada, especifícala aquí:
                // Rule::exists('oracle_academpostulgrado.ACTUALIZACION_ENLACE', 'ACEN_ID')->where('ACEN_USADO','N'),
                Rule::exists('oracle_academpostulgrado.ACTUALIZACION_ENLACE', 'ACEN_ID'),
            ],
            'programa_id' => ['required','integer'],
            'proceso_id' => ['required','integer'],
            'codigo'     => ['required','string','max:50'],

            // Anexos prioritarios
            'doc_identificacion' => ['required','file','mimes:pdf,jpg,jpeg,png','max:3072'],
            'cert_saber'         => ['nullable','file','mimetypes:application/pdf','max:4096'],
            'codigo_saber'       => ['nullable','string','max:50'],

            // Contacto y vínculos
            'grupo_investigacion' => ['required', Rule::in(['SI','NO'])],
            'nombre_grupo'        => ['nullable','string','max:150'],

            'telefono'            => ['required','regex:/^[0-9]{7,15}$/'],
            'correo_personal'     => ['nullable','email:rfc,dns','max:150'],

            'departamento'        => ['required','string','max:100'],
            'ciudad'              => ['nullable','string','max:100'], // se valida condicionalmente abajo
            'direccion'           => ['required','string','max:255'],

            'hijo_funcionario'    => ['nullable', Rule::in(['SI','NO'])],
            'hijo_docente'        => ['nullable', Rule::in(['SI','NO'])],
            'es_funcionario'      => ['nullable', Rule::in(['SI','NO'])],
            'es_docente'          => ['nullable', Rule::in(['SI','NO'])],

            // Bandera de postgrado
            'es_postgrado'        => ['required','boolean'],

            // Sección postgrado (condicional)
            'titulo_pregrado'       => ['nullable','string','max:200'],
            'universidad_pregrado'  => ['nullable','string','max:200'],
            'fecha_grado_pregrado'  => ['nullable','date','before_or_equal:today'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $data = $this->all();

            // 1) Si grupo_investigacion == 'SI', nombre_grupo es obligatorio
            if (($data['grupo_investigacion'] ?? '') === 'SI') {
                if (blank($data['nombre_grupo'] ?? '')) {
                    $v->errors()->add('nombre_grupo', 'Este campo es obligatorio cuando pertenece a un grupo de investigación.');
                }
            }

            // 2) Ciudad obligatoria salvo “San Andrés y Providencia”
            $dep = (string)($data['departamento'] ?? '');
            $depNorm = (string) Str::of($dep)->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '');
            $sanAndresNorm = (string) Str::of('San Andrés y Providencia')->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '');
            if ($depNorm !== $sanAndresNorm && blank($data['ciudad'] ?? '')) {
                $v->errors()->add('ciudad', 'La ciudad es obligatoria para el departamento seleccionado.');
            }

            // 3) Si es postgrado, obliga universidad_pregrado
            $esPost = filter_var($data['es_postgrado'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($esPost) {
                if (blank($data['universidad_pregrado'] ?? '')) {
                    $v->errors()->add('universidad_pregrado', 'Universidad de egreso (pregrado) es obligatorio para procesos de postgrado.');
                }
            }

            // 4) Si universidad_pregrado es UCMC, título y fecha son obligatorios
            if (!blank($data['universidad_pregrado'] ?? '')) {
                $norm = (string) Str::of($data['universidad_pregrado'])->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '');
                $targets = [
                    (string) Str::of('Universidad Colegio Mayor de Cundinamarca')->ascii()->lower()->replaceMatches('/[^a-z0-9]/', ''),
                    (string) Str::of('Universidad-Colegio Mayor de Cundinamarca')->ascii()->lower()->replaceMatches('/[^a-z0-9]/', ''),
                ];
                if (in_array($norm, $targets, true)) {
                    if (blank($data['titulo_pregrado'] ?? '')) {
                        $v->errors()->add('titulo_pregrado', 'Este campo es obligatorio para egresados de la Universidad Colegio Mayor de Cundinamarca.');
                    }
                    if (blank($data['fecha_grado_pregrado'] ?? '')) {
                        $v->errors()->add('fecha_grado_pregrado', 'La fecha de grado es obligatoria para egresados de la Universidad Colegio Mayor de Cundinamarca.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'enlace_id.required' => 'Falta el identificador del enlace.',
            'enlace_id.integer'  => 'El identificador del enlace no es válido.',

            'proceso_id.required' => 'Falta el identificador del proceso.',
            'proceso_id.integer'  => 'El identificador del proceso no es válido.',

            'programa_id.required' => 'Falta el identificador del programa.',
            'programa_id.integer'  => 'El identificador del programa no es válido.',            

            'codigo.required' => 'Falta el código del estudiante.',

            'doc_identificacion.required' => 'Debe adjuntar el documento de identificación.',
            'doc_identificacion.file'     => 'El documento de identificación no es válido.',
            'doc_identificacion.mimes'    => 'Formatos permitidos: PDF, JPG, JPEG o PNG.',
            'doc_identificacion.max'      => 'El documento de identificación no debe exceder 3 MB.',

            'cert_saber.mimetypes' => 'El certificado de SaberPro/TyT debe ser un PDF.',
            'cert_saber.max'       => 'El certificado no debe exceder 4 MB.',

            'grupo_investigacion.required' => 'Indique si pertenece a un grupo de investigación.',
            'grupo_investigacion.in'       => 'Valor no válido (use SI o NO).',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex'    => 'El teléfono debe tener entre 7 y 15 dígitos (solo números).',

            'correo_personal.email' => 'El correo personal no es válido.',
            'correo_personal.max'   => 'El correo personal es demasiado largo.',

            'departamento.required' => 'El departamento es obligatorio.',

            'direccion.required' => 'La dirección es obligatoria.',

            'hijo_funcionario.in' => 'Valor no válido (use SI o NO).',
            'hijo_docente.in'     => 'Valor no válido (use SI o NO).',
            'es_funcionario.in'   => 'Valor no válido (use SI o NO).',
            'es_docente.in'       => 'Valor no válido (use SI o NO).',

            'es_postgrado.required' => 'No se pudo determinar si el proceso es de postgrado.',
            'es_postgrado.boolean'  => 'Valor inválido para postgrado.',

            'titulo_pregrado.max'      => 'El título de pregrado es demasiado largo.',
            'universidad_pregrado.max' => 'La universidad de pregrado es demasiado larga.',
            'fecha_grado_pregrado.date'=> 'La fecha de grado no es válida.',
            'fecha_grado_pregrado.before_or_equal' => 'La fecha de grado no puede ser futura.',
        ];
    }
}
