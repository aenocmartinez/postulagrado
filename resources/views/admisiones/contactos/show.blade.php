@extends('layouts.app')

@section('title', 'Detalles del Contacto')

@section('header', 'Detalles del Contacto')

@section('content')

<h2 class="text-lg font-semibold text-gray-800">Información del Contacto</h2>

<div class="flex justify-center mt-12">
    <div class="w-full max-w-2xl bg-white rounded-xl p-10">

        <!-- Información Principal -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $contacto['nombre'] }}</h1>
            <p class="text-lg text-gray-800 mt-1">{{ $contacto['programaNombre'] }}</p>
        </div>

        <!-- Contenedor de Información -->
        <div class="mt-6 px-6 text-gray-800">
            <p><strong>Correo:</strong> {{ $contacto['email'] }}</p>
            <p class="mt-2"><strong>Teléfono:</strong> {{ $contacto['telefono'] }}</p>
        </div>

        <!-- Observaciones -->
        @if($contacto['observacion'])
            <div class="mt-6 px-6">
                <h3 class="text-lg font-semibold">Observaciones</h3>
                <p class="text-base mt-2">{{ $contacto['observacion'] }}</p>
            </div>
        @endif

    </div>    
</div>

<div class="flex justify-end space-x-2 mt-3">
    <a href="{{ route('contactos.edit', $contacto['id']) }}"
        class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 transition">
        Editar
    </a>

    <form action="{{ route('contactos.destroy', $contacto['id']) }}" method="POST" 
        onsubmit="return confirmarEliminacion(event)">
        @csrf
        @method('DELETE')
        <button type="submit" 
            class="px-4 py-2 border border-red-400 text-red-600 rounded-md bg-white hover:bg-red-50 hover:border-red-700 transition">
            Eliminar
        </button>
    </form>                
</div>

<!-- Script para Confirmación de Eliminación -->
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminacion(event) {
        event.preventDefault();
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d32f2f",
            cancelButtonColor: "#6b7280",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
</script>
@endsection

@endsection
