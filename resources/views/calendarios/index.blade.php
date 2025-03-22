@extends('layouts.app')

@section('title', 'Gestión de Actividades')

@section('header', 'Gestión de Actividades')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto">
    
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
        <button onclick="toggleFormulario()" id="btn-nueva-actividad" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
            + Nueva Actividad
        </button>
    </div>

    <!-- Sección para agregar o editar una actividad -->
    <div id="formulario-actividad" class="mb-6 p-4 bg-gray-100 rounded-lg hidden">
        <h3 id="form-title" class="text-md font-semibold text-gray-700 mb-3">Agregar Nueva Actividad</h3>

        <form id="actividad-form" action="{{ route('actividades.store', ['id' => $proceso->getId()]) }}" method="POST" class="space-y-4">
            @csrf
            @method('POST')

            <!-- ID de la actividad (Hidden) -->
            <input type="hidden" name="actividad_id" id="actividad_id">

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
                        value="{{ old('fecha_inicio') }}">
                    
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
                        value="{{ old('fecha_fin') }}">
                    
                    @error('fecha_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" id="submit-btn"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Agregar Actividad
            </button>
        </form>
    </div>

    <!-- Listado de actividades -->
    <div>
        <h3 class="text-md font-semibold text-gray-700 mb-3">Actividades del Calendario</h3>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium w-80">Descripción</th>
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
                            <td class="px-4 py-2 text-gray-900 w-80 break-words">
                                {{ $actividad->getDescripcion() }}
                            </td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad->getFechaInicio() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $actividad->getFechaFin() }}</td>
                            <td class="px-4 py-2 font-semibold {{ $colorEstado }}">{{ $estado }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <button type="button" class="editar-btn hover:text-blue-600 transition"
                                            data-id="{{ $actividad->getId() }}"
                                            data-descripcion="{{ $actividad->getDescripcion() }}"
                                            data-inicio="{{ $actividad->getFechaInicio() }}"
                                            data-fin="{{ $actividad->getFechaFin() }}">
                                        Editar
                                    </button>

                                    <button type="button" class="eliminar-btn hover:text-red-600 transition"
                                            data-url="{{ route('actividades.destroy', ['id' => $proceso->getId(), 'actividad' => $actividad->getId()]) }}">
                                        Eliminar
                                    </button>
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

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        @if ($errors->any())
            document.getElementById('formulario-actividad').classList.remove('hidden');
        @endif

        document.querySelectorAll(".eliminar-btn").forEach((button) => {
            button.addEventListener("click", function () {
                let url = this.dataset.url;
                confirmarEliminacion(url);
            });
        });

        document.querySelectorAll(".editar-btn").forEach((button) => {
            button.addEventListener("click", function () {
                document.getElementById('actividad_id').value = this.dataset.id;
                document.getElementById('descripcion').value = this.dataset.descripcion;
                document.getElementById('fecha_inicio').value = this.dataset.inicio;
                document.getElementById('fecha_fin').value = this.dataset.fin;
                document.getElementById('formulario-actividad').classList.remove('hidden');
                document.getElementById('submit-btn').innerText = "Actualizar Actividad";
            });
        });
    });

    function toggleFormulario() {
        document.getElementById('formulario-actividad').classList.toggle('hidden');
        document.getElementById('actividad_id').value = "";
        document.getElementById('descripcion').value = "";
        document.getElementById('fecha_inicio').value = "";
        document.getElementById('fecha_fin').value = "";
        document.getElementById('submit-btn').innerText = "Agregar Actividad";
    }

    function confirmarEliminacion(url) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6b7280",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement("form");
                form.action = url;
                form.method = "POST";
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }    

</script>
@endsection
