@extends('layouts.app')

@section('title', 'Listado de Procesos')

@section('header', 'Seleccione un Proceso')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 w-full max-w-3xl mx-auto">

    <label for="proceso_id" class="block text-sm font-medium text-gray-700 mb-2">
        Buscar y seleccionar un proceso:
    </label>

    <select id="proceso_id" class="select2 w-full border border-gray-300 px-4 py-2 rounded-md text-xs">
        <option value="">-- Seleccione --</option>
        @foreach ($procesos as $proceso)
            <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
        @endforeach
    </select>

</div>
@endsection

@section('scripts')
    {{-- Select2 CSS & JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar Select2 con búsqueda habilitada
            $('#proceso_id').select2({
                placeholder: 'Buscar proceso...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: () => 'No se encontraron resultados',
                    inputTooShort: () => 'Escriba para buscar'
                }
            });

            // Redirección automática al seleccionar
            $('#proceso_id').on('change', function () {
                const id = $(this).val();
                if (id) {
                    window.location.href = `/notificaciones/proceso/${id}`;
                }
            });
        });
    </script>
@endsection
