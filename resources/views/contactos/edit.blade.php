@extends('layouts.app')

@section('title', 'Editar Contacto')

@section('header', 'Editar Contacto')

@section('content')

    <div class="bg-white rounded-lg p-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Editar Contacto</h2>

        <!-- Formulario -->
        <form method="POST" action="{{ route('contactos.update', $contacto->getId()) }}">
            @csrf
            @method('PUT')

            @include('contactos.partials.form', ['contacto' => $contacto])

            <!-- Botones -->
            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('contactos.index') }}"
                   class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                    Guardar Cambios
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#programa_id').select2({
            placeholder: "Seleccione un programa",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
