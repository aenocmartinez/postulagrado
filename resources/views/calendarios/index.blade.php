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
        <h2 class="text-lg font-semibold text-gray-800">Proceso: Proceso de Grado 2025-1</h2>
        <p class="text-sm text-gray-600">Nivel Educativo: <strong>Postgrado</strong></p>
        <p class="text-sm text-gray-600">Estado: 
            <span class="text-green-600">Abierto</span>
        </p>
    </div>

    <!-- Botón para mostrar el formulario alineado a la derecha -->
    <div class="mb-4 flex justify-end">
        <button onclick="toggleFormulario()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            + Nueva Actividad
        </button>
    </div>

    <!-- Sección para agregar una nueva actividad (Oculta por defecto) -->
    <div id="formulario-actividad" class="mb-6 p-4 bg-gray-100 rounded-lg hidden">
        <h3 class="text-md font-semibold text-gray-700 mb-3">Agregar Nueva Actividad</h3>

        <form action="#" method="POST" class="space-y-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Actividad</label>
                <input type="text" name="nombre" id="nombre"
                    class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none"
                    required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio"
                        class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none"
                        required>
                </div>

                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin"
                        class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none"
                        required>
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
                        <th class="px-4 py-2 font-medium">Nombre</th>
                        <th class="px-4 py-2 font-medium">Fecha de Inicio</th>
                        <th class="px-4 py-2 font-medium">Fecha de Fin</th>
                        <th class="px-4 py-2 font-medium">Estado</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $fechaHoy = now()->format('Y-m-d');
                        $actividades = [
                            ['nombre' => 'Registro de Aspirantes', 'inicio' => '2025-05-01', 'fin' => '2025-05-10'],
                            ['nombre' => 'Recepción de Documentos', 'inicio' => '2025-06-05', 'fin' => '2025-06-15'],
                            ['nombre' => 'Evaluaciones', 'inicio' => '2025-04-01', 'fin' => '2025-04-05'],
                            ['nombre' => 'Revisión de Actas', 'inicio' => '2025-03-10', 'fin' => '2025-03-15'],
                            ['nombre' => 'Entrega de Resultados', 'inicio' => '2025-03-20', 'fin' => '2025-03-25'],
                            ['nombre' => 'Inscripción a Grados', 'inicio' => '2025-04-10', 'fin' => '2025-04-15'],
                            ['nombre' => 'Publicación de Admitidos', 'inicio' => '2025-02-01', 'fin' => '2025-02-05'],
                            ['nombre' => 'Revisión de Pagos', 'inicio' => '2025-02-15', 'fin' => '2025-02-20'],
                            ['nombre' => 'Ceremonia de Grado', 'inicio' => '2025-01-10', 'fin' => '2025-01-10'],
                            ['nombre' => 'Entrega de Diplomas', 'inicio' => '2025-01-15', 'fin' => '2025-01-20']
                        ];
                    @endphp

                    @foreach($actividades as $actividad)
                        @php
                            if ($fechaHoy < $actividad['inicio']) {
                                $estado = 'Programada';
                                $colorEstado = 'text-yellow-600';
                            } elseif ($fechaHoy >= $actividad['inicio'] && $fechaHoy <= $actividad['fin']) {
                                $estado = 'En curso';
                                $colorEstado = 'text-green-600';
                            } else {
                                $estado = 'Finalizada';
                                $colorEstado = 'text-gray-600';
                            }
                        @endphp

                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-900">{{ $actividad['nombre'] }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad['inicio'] }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad['fin'] }}</td>
                            <td class="px-4 py-2 font-semibold {{ $colorEstado }}">{{ $estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <a href="#" class="hover:text-blue-600 transition">Editar</a>
                                    <button type="button" class="hover:text-red-600 transition">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
