@extends('layouts.app')

@section('title', 'Editar Proceso de Grado')

@section('header', 'Editar Proceso de Grado')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-3xl mx-auto">
    
    <!-- Título -->
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Editar Proceso de Grado</h2>

    <!-- Formulario -->
    <form action="{{ route('procesos.update', $proceso->getId()) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        @include('procesos.partials.form')

        <br>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-2">
            <a href="{{ route('procesos.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Actualizar
            </button>
        </div>

    </form>

</div>

@endsection
