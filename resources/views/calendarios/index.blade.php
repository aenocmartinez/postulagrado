@extends('layouts.app')

@section('title', 'Gestión de Actividades')

@section('header', 'Gestión de Actividades')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto">
    
    <!-- Encabezado con botón "Volver a la lista de procesos" -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Gestión de Actividades</h2>
        <a href="{{ route('procesos.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
            ← Volver a la lista de procesos
        </a>
    </div>

    <!-- Información del proceso -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Proceso: {{ $proceso->getNombre() }}</h2>
        <p class="text-sm text-gray-600">Nivel Educativo: <strong>{{ $proceso->getNivelEducativo() }}</strong></p>
        <p class="text-sm text-gray-600">Estado: 
            <span class="text-green-600">{{$proceso->getEstado() }}</span>
        </p>
    </div>

    <!-- Botón para mostrar el formulario alineado a la derecha -->
    <div class="mb-4 flex justify-end">
        <button onclick="toggleFormulario()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            + Nueva Actividad
        </button>
    </div>

    <!-- Sección para agregar una nueva actividad (Oculta por defecto) -->
    <div id="formulario-actividad" class="mb-6 p-4 bg-gray-100 rounded-lg @if ($errors->any()) block @else hidden @endif">
        <h3 class="text-md font-semibold text-gray-700 mb-3">Agregar Nueva Actividad</h3>

        <form action="{{ route('actividades.store', ['id' => $proceso->getId()]) }}" method="POST" class="space-y-4">
            @csrf

            <!-- Descripción -->
            <div>
                <textarea name="descripcion" id="descripcion" rows="3"
                    class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none resize-none
                    @error('descripcion') border-red-500 @enderror"
                    placeholder="Ingrese la actividad...">{{ old('descripcion') }}</textarea>
                
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                        class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none
                        @error('fecha_inicio') border-red-500 @enderror"
                        value="{{ old('fecha_inicio', isset($actividad) ? \Carbon\Carbon::parse($actividad->getFechaInicio())->format('Y-m-d') : '') }}">
                    
                    @error('fecha_inicio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                        class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none
                        @error('fecha_fin') border-red-500 @enderror"
                        value="{{ old('fecha_fin', isset($actividad) ? \Carbon\Carbon::parse($actividad->getFechaFin())->format('Y-m-d') : '') }}">
                    
                    @error('fecha_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Agregar Actividad
            </button>
        </form>
    </div>

    <!-- Listado de actividades con información de prueba -->
    <div>
        <h3 class="text-md font-semibold text-gray-700 mb-3">Actividades del Calendario</h3>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium w-80">Descripción</th> <!-- Definiendo ancho fijo -->
                        <th class="px-4 py-2 font-medium">Fecha de Inicio</th>
                        <th class="px-4 py-2 font-medium">Fecha de Fin</th>
                        <th class="px-4 py-2 font-medium">Estado</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $fechaHoy = now()->format('Y-m-d');
                    @endphp

                    @forelse($proceso->getActividades() as $actividad)
                        @php
                            if ($fechaHoy < $actividad->getFechaInicio()) {
                                $estado = 'Programada';
                                $colorEstado = 'text-yellow-600';
                            } elseif ($fechaHoy >= $actividad->getFechaInicio() && $fechaHoy <= $actividad->getFechaFin()) {
                                $estado = 'En curso';
                                $colorEstado = 'text-green-600';
                            } else {
                                $estado = 'Finalizada';
                                $colorEstado = 'text-gray-600';
                            }
                        @endphp

                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <!-- Descripción con ancho fijo -->
                            <td class="px-4 py-2 text-gray-900 w-80 break-words">
                                {{ $actividad->getDescripcion() }}
                            </td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad->getFechaInicio() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad->getFechaFin() }}</td>
                            <td class="px-4 py-2 font-semibold {{ $colorEstado }}">{{ $estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <a href="#" class="hover:text-blue-600 transition">Editar</a>
                                    <button type="button" class="hover:text-red-600 transition">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No se encontraron actividades.
                        </td>
                    </tr>                        
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    function toggleFormulario() {
        let form = document.getElementById('formulario-actividad');
        form.classList.toggle('hidden');
    }
</script>

@endsection
