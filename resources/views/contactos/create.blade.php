@extends('layouts.app')

@section('title', 'Crear Contacto')

@section('header', 'Nuevo Contacto')

@section('content')

    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Crear Nuevo Contacto</h2>

        <!-- Formulario -->
        <form method="POST" action="{{ route('contactos.store') }}">
            @csrf

            <!-- Nombre -->
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none">
                @error('nombre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div class="mb-4">
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none">
                @error('telefono')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Observación -->
            <div class="mb-4">
                <label for="observacion" class="block text-sm font-medium text-gray-700">Observación</label>
                <textarea name="observacion" id="observacion" rows="3"
                          class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none">{{ old('observacion') }}</textarea>
                @error('observacion')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selección de Programa con Búsqueda -->
            <div class="mb-4">
                <label for="programa_id" class="block text-sm font-medium text-gray-700">Programa</label>
                <select name="programa_id" id="programa_id"
                        class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-gray-400 outline-none">
                    <option value="">Seleccione un programa</option>
                    @foreach ($programas as $programa)
                        <option value="{{ $programa->getId() }}" {{ old('programa_id') == $programa->getId() ? 'selected' : '' }}>
                            {{ $programa->getNombre() }}
                        </option>
                    @endforeach
                </select>
                @error('programa_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-2">
                <a href="{{ route('contactos.index') }}"
                   class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                    Guardar
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


