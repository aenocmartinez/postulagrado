@extends('layouts.app')

@section('title', 'Gestión de Actividades')

@section('header', 'Gestión de Actividades')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto">

    <!-- Información del proceso -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">{{ $procesoActividad->nombreProceso }}</h2>
        <p class="text-sm text-gray-600"><strong>{{ $procesoActividad->nombreNivelEducativo }}</strong></p>
        <p class="text-sm text-gray-600">
            <span class="text-green-600">{{ $procesoActividad->estadoProceso }}</span>
        </p>
    </div>

    <!-- Botón para mostrar el formulario -->
    <div class="mb-4 flex justify-end">
        <button onclick="toggleFormulario()" id="btn-nueva-actividad" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
            + Nueva Actividad
        </button>
    </div>

    <!-- Formulario de agregar/editar actividad -->
    <div id="formulario-actividad" class="mb-6 p-4 bg-gray-100 rounded-lg hidden">
        <h3 id="form-title" class="text-md font-semibold text-gray-700 mb-3">Agregar Nueva Actividad</h3>

        <div class="space-y-4">
            <!-- Descripción -->
            <div>
                <textarea id="descripcion" rows="3"
                    class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none resize-none"
                    placeholder="Ingrese la actividad..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio"
                        class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none">
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                    <input type="date" id="fecha_fin"
                        class="border px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none">
                </div>
            </div>

            <button type="button" onclick="agregarActividadTemporal()" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Agregar Actividad
            </button>
        </div>
    </div>

    <!-- Tabla de actividades -->
    <div>
        <h3 class="text-md font-semibold text-gray-700 mb-3">Actividades del Calendario</h3>

        <div class="overflow-x-auto">
            <table id="tabla-actividades" class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium w-80">Descripción</th>
                        <th class="px-4 py-2 font-medium">Fecha de Inicio</th>
                        <th class="px-4 py-2 font-medium">Fecha de Fin</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se llenarán las actividades -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botón para guardar todas las actividades -->
    <div class="flex justify-end mt-6">
        <form id="guardar-todas-form" action="{{ route('actividades.store', ['id' => $procesoActividad->procesoID]) }}" method="POST">
            @csrf
            <input type="hidden" name="actividades" id="actividades-json">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Guardar cambios y notificar
            </button>
        </form>
    </div>

</div>

@endsection

@section('scripts')

@php
    $actividadesTemporales = [];

    foreach ($procesoActividad->actividades as $actividad) {        
        $actividadesTemporales[] = [
            'id' => $actividad['id'],
            'descripcion' => $actividad['nombre'],
            'fecha_inicio' => $actividad['fechaInicio'],
            'fecha_fin' => $actividad['fechaFin'],
        ];
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let actividadesTemporales = @json($actividadesTemporales);

    let actividadEditando = null; // Índice que estamos editando

    document.addEventListener('DOMContentLoaded', function () {
        pintarTabla();
    });

    function toggleFormulario() {
        const formulario = document.getElementById('formulario-actividad');
        formulario.classList.toggle('hidden');
        limpiarFormulario();
        actividadEditando = null;
        document.getElementById('form-title').innerText = "Agregar Nueva Actividad";
        document.querySelector('#formulario-actividad button').innerText = "Agregar Actividad";
    }

    function limpiarFormulario() {
        document.getElementById('descripcion').value = "";
        document.getElementById('fecha_inicio').value = "";
        document.getElementById('fecha_fin').value = "";
    }

    function agregarActividadTemporal() {
        const descripcion = document.getElementById('descripcion').value.trim();
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        if (descripcion === "" || fechaInicio === "" || fechaFin === "") {
            Swal.fire({
                icon: "warning",
                title: "Campos incompletos",
                text: "Por favor completa todos los campos antes de agregar o actualizar.",
            });
            return;
        }

        if (actividadEditando !== null) {
            actividadesTemporales[actividadEditando] = {
                id: actividadesTemporales[actividadEditando].id ?? 0,
                descripcion: descripcion,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            };
            actividadEditando = null;
            document.getElementById('form-title').innerText = "Agregar Nueva Actividad";
            document.querySelector('#formulario-actividad button').innerText = "Agregar Actividad";
        } else {
            actividadesTemporales.push({
                id: 0,
                descripcion: descripcion,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });
        }

        limpiarFormulario();
        toggleFormulario();
        pintarTabla();
    }

    function pintarTabla() {
        const tbody = document.querySelector("#tabla-actividades tbody");
        tbody.innerHTML = "";

        actividadesTemporales.forEach((actividad, index) => {
            const fila = document.createElement('tr');
            fila.className = "border-b border-gray-300 bg-white hover:bg-gray-100 transition";
            fila.innerHTML = `
                <td class="px-4 py-2 text-gray-900 w-80 break-words">${actividad.descripcion}</td>
                <td class="px-4 py-2 text-gray-900">${actividad.fecha_inicio}</td>
                <td class="px-4 py-2 text-gray-900">${actividad.fecha_fin}</td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-blue-600 hover:text-blue-800 mr-2" onclick="editarActividad(${index})">
                        Editar
                    </button>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="eliminarActividad(${index})">
                        Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(fila);
        });

        actualizarInputOculto();
    }

    function eliminarActividad(index) {
        actividadesTemporales.splice(index, 1);
        pintarTabla();
    }

    function editarActividad(index) {
        const actividad = actividadesTemporales[index];
        document.getElementById('descripcion').value = actividad.descripcion;
        document.getElementById('fecha_inicio').value = actividad.fecha_inicio;
        document.getElementById('fecha_fin').value = actividad.fecha_fin;

        actividadEditando = index;

        document.getElementById('formulario-actividad').classList.remove('hidden');
        document.getElementById('form-title').innerText = "Editar Actividad";
        document.querySelector('#formulario-actividad button').innerText = "Actualizar Actividad";
    }

    function actualizarInputOculto() {
        document.getElementById('actividades-json').value = JSON.stringify(actividadesTemporales);
    }
</script>
@endsection
