@extends('layouts.app')

@section('title', 'Adjuntar Documento al Proceso')

@section('header', 'Adjuntar Documento')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-3xl mx-auto">

    <form action="{{ route('proceso_documentos.store', $procesoID) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Campo oculto para proceso ID -->
        <input type="hidden" name="proceso_id" value="{{ $procesoID }}">

        <!-- Nombre del documento -->
        <div class="mb-6">
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del documento</label>
            <input type="text" name="nombre" id="nombre"
                value="{{ old('nombre') }}"
                class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none @error('nombre') border-red-500 @enderror"
                placeholder="Ej: Instructivo de Inscripción">

            @error('nombre')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Archivo -->
        <div class="mb-6">
            <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">Archivo</label>
            <input type="file" name="archivo" id="archivo"
                class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none bg-white @error('archivo') border-red-500 @enderror"
                 accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">

            @error('archivo')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <small class="text-gray-500 mt-1 block">Formatos permitidos: pdf, doc, docx, xls, xlsx, jpg, jpeg, png.</small>
        </div>

        <!-- Botón Guardar -->
        <div class="flex justify-end mt-4">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Guardar
            </button>
        </div>

    </form>

</div>

@endsection
