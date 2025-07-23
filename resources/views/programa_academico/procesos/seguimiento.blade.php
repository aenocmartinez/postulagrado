@extends('layouts.programa_academico')

@section('title', 'Seguimiento del Programa')

@section('header', 'Seguimiento del Proceso')

@section('content')

@php
    $actividadesPorEstado = $proceso->getActividadesPorEstadoTemporal();

    $conteo = collect($actividadesPorEstado)->map(fn($items) => count($items));

    $enCurso     = $actividadesPorEstado['EnCurso'] ?? [];
    $finalizadas = $actividadesPorEstado['Finalizadas'] ?? [];
    $programadas = $actividadesPorEstado['Programadas'] ?? [];
    $proximas    = $actividadesPorEstado['ProximasIniciar'] ?? [];

    // Cálculo porcentaje de avance del proceso
    $total = $conteo->sum();
    $totalFinalizadas = $conteo['Finalizadas'] ?? 0;
    $porcentajeAvanceDelProceso = $total > 0 ? round(($totalFinalizadas / $total) * 100, 1) : 0;
@endphp


<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-6xl mx-auto">

    <!-- 📌 Información del Programa -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-blue-900">{{ auth()->user()->programaAcademico()->getNombre() }}</h2>
        <p class="text-sm text-gray-600">Unidad Regional: <strong>{{ auth()->user()->programaAcademico()->getUnidadRegional()->getNombre() }}</strong></p>
        <p class="text-sm text-gray-600">Código SNIES: <strong>{{ auth()->user()->programaAcademico()->getSnies() }}</strong></p>
        <p class="text-sm text-gray-600">Estado del Proceso: <span class="text-green-600">{{ $proceso->getEstado() }}</span></p>
    </div>

    <!-- 📊 Avance del Programa -->
    <h3 class="text-md font-semibold text-gray-700 mb-2">Avance del Proceso</h3>
    <div class="w-full bg-gray-300 rounded h-4 mb-1">
        <div class="h-4 bg-blue-600 rounded" style="width: {{ $porcentajeAvanceDelProceso }}%"></div>
    </div>
    <p class="text-xs text-gray-500 text-right">{{ $porcentajeAvanceDelProceso }}% completado</p>

    <hr class="my-8 border-t border-gray-300">

    <!-- 🗓️ Actividades del Proceso -->
    <h3 class="text-md font-semibold text-gray-700 mb-2">Actividades del Proceso</h3>

    <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
        <span><span class="inline-block w-3 h-3 bg-green-300 rounded-full mr-1"></span> En Curso</span>
        <span><span class="inline-block w-3 h-3 bg-gray-300 rounded-full mr-1"></span> Finalizadas</span>
        <span><span class="inline-block w-3 h-3 bg-yellow-300 rounded-full mr-1"></span> Programadas</span>
        <span><span class="inline-block w-3 h-3 bg-orange-300 rounded-full mr-1"></span> Próximas</span>
    </div>

    <p class="text-sm text-gray-500 mb-2">
        Se han registrado <strong>{{ $conteo->sum() }} actividades</strong> en este proceso
        ({{ $conteo['EnCurso'] ?? 0 }} en curso, {{ $conteo['Finalizadas'] ?? 0 }} finalizadas, {{ $conteo['Programadas'] ?? 0 }} programadas, {{ $conteo['ProximasIniciar'] ?? 0 }} próximas).
    </p>

    <div class="grid grid-cols-2 gap-6 text-sm mb-6">
        {{-- En Curso --}}
        <div class="bg-green-100 p-4 rounded-lg">
            <h4 class="font-semibold mb-2 text-green-700">🔹 En Curso</h4>
            <ul class="list-disc pl-4 text-xs">
                @forelse($enCurso as $actividad)
                    <li class="mb-2 text-xs">
                        {{ $actividad->getDescripcion() }} <br>
                        <span class="text-blue-700">
                        Periodo: {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaInicio()) }} al {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaFin()) }}
                        </span>
                    </li>
                @empty
                    <li>No hay actividades en curso.</li>
                @endforelse
            </ul>
        </div>

        {{-- Finalizadas --}}
        <div class="bg-gray-100 p-4 rounded-lg">
            <h4 class="font-semibold mb-2 text-gray-800">🔹 Finalizadas</h4>
            <ul class="list-disc pl-4 text-xs">
                @forelse($finalizadas as $actividad)
                    <li class="mb-2 text-xs">
                        {{ $actividad->getDescripcion() }} <br> 
                        <span class="text-blue-700">Finalizó el {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaFin()) }} </span>
                    </li>
                @empty
                    <li>No hay actividades finalizadas.</li>
                @endforelse
            </ul>
        </div>

        {{-- Programadas --}}
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h4 class="font-semibold mb-2 text-yellow-700">🔹 Programadas</h4>
            <ul class="list-disc pl-4 text-xs">
                @forelse($programadas as $actividad)
                    <li class="mb-2 text-xs">
                        {{ $actividad->getDescripcion() }} <br>
                        <span class="text-blue-700">
                        Periodo: {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaInicio()) }} al {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaFin()) }}
                        </span>
                    </li>
                @empty
                    <li>No hay actividades programadas.</li>
                @endforelse
            </ul>
        </div>

        {{-- Próximas a Iniciar --}}
        <div class="bg-orange-100 p-4 rounded-lg">
            <h4 class="font-semibold mb-2 text-orange-700">🔹 Próximas a Iniciar</h4>
            <ul class="list-disc pl-4 text-xs">
                @forelse($proximas as $actividad)
                    <li class="mb-2 text-xs">
                        {{ $actividad->getDescripcion() }} <br>
                        <span class="text-blue-700">
                        Periodo: {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaInicio()) }} al {{ \Src\shared\formato\FormatoFecha::formatearFechaLarga($actividad->getFechaFin()) }}
                        </span>
                    </li>
                @empty
                    <li>No hay actividades próximas a iniciar.</li>
                @endforelse
            </ul>
        </div>
    </div>


    <hr class="my-8 border-t border-gray-300">

    <!-- 👥 Gestión de Estudiantes -->
    <h3 class="text-md font-semibold text-gray-700 mb-2">Estudiantes Candidatos a Grado</h3>
    <div class="border border-gray-300 p-6 rounded-md bg-gray-50 text-center mb-6" id="seccion-estudiantes-vinculados">
        @include('programa_academico.procesos.seccion-boton-estudiantes', ['proceso' => $proceso])
    </div>

    <template id="template-boton-estudiantes">
        @include('programa_academico.procesos.seccion-boton-estudiantes', ['proceso' => $proceso])
    </template>

    <hr class="my-8 border-t border-gray-300">

<!-- 🧾 Estado por Área -->
<h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Estado de Paz y Salvo</h3>
<div class="grid grid-cols-2 gap-6">
    @php
        $pazYSalvo = [
            'Financiera' => ['total' => 100, 'pendientes' => 10],
            'Admisiones' => ['total' => 100, 'pendientes' => 5],
            'Biblioteca' => ['total' => 100, 'pendientes' => 23],
            'Recursos educativos' => ['total' => 100, 'pendientes' => 15],
            'Centro de idiomas' => ['total' => 100, 'pendientes' => 30],
        ];
    @endphp

    @foreach ($pazYSalvo as $area => $datos)
        @php
            $porcentaje = (1 - $datos['pendientes'] / $datos['total']) * 100;
        @endphp
        <div class="bg-gray-200 p-4 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">🏛️ {{ $area }}</h4>
            <p class="text-xs text-gray-600">Total de Estudiantes: <strong>{{ $datos['total'] }}</strong></p>
            <p class="text-xs text-gray-600">Pendientes: <strong class="text-red-600">{{ $datos['pendientes'] }}</strong></p>
            <div class="w-full bg-gray-300 rounded mt-2">
                <div class="h-2 bg-blue-500 rounded" style="width: {{ $porcentaje }}%"></div>
            </div>
            <p class="text-xs text-right text-gray-500 mt-1">{{ number_format($porcentaje, 1) }}% en paz y salvo</p>
        </div>
    @endforeach
</div>



    <hr class="my-8 border-t border-gray-300">
    
    <!-- 📩 Notificaciones -->
    @section('notificaciones')
    <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200 text-sm h-[400px] overflow-y-auto">
        <h3 class="text-gray-800 text-sm font-semibold mb-3">Notificaciones del programa</h3>

        {{-- Recientes (no leídas) --}}
        <div class="mb-5">
            <h4 class="text-[12px] font-semibold text-blue-800 mb-2 border-b pb-1 text-right">Recientes</h4>
            <ul id="lista-no-leidas" class="space-y-3 text-xs text-gray-800">
                @forelse($notificaciones as $notificacion)
                    @if(!$notificacion->fueLeida())
                        <li class="flex justify-between items-center border-b pb-2 cursor-pointer"
                            onclick="mostrarContenido(this)"
                            data-id="{{ $notificacion->getId() }}"
                            data-titulo="{{ e($notificacion->getAsunto()) }}"
                            data-mensaje="{!! e($notificacion->getMensaje()) !!}">
                            <div>
                                <p class="font-medium">{{ $notificacion->getAsunto() }}</p>
                                <p class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($notificacion->getFechaCreacion())->format('d/m/Y') }}</p>
                            </div>
                            <button title="Marcar como vista"
                                    onclick="marcarComoLeida(event, this)"
                                    class="text-blue-600 hover:text-green-600 transition">
                                <i class="fas fa-check-double text-sm"></i>
                            </button>
                        </li>
                    @endif
                @empty
                    <li class="text-xs text-gray-500">No hay notificaciones para mostrar.</li>
                @endforelse
            </ul>
            
            <p id="sin-recientes" class="text-xs text-gray-500 hidden">No hay notificaciones recientes para mostrar.</p>

        </div>

        {{-- Revisadas o Vistas --}}
        <div>
            <h4 class="text-[12px] font-semibold text-blue-800 mb-2 border-b pb-1 text-right">Notificaciones revisadas</h4>
            <ul id="lista-leidas" class="space-y-3 text-xs text-gray-700">
                @foreach($notificaciones as $notificacion)
                    @if($notificacion->fueLeida())
                        <li class="flex justify-between items-center border-b pb-2 cursor-pointer"
                            onclick="mostrarContenido(this)"
                            data-titulo="{{ e($notificacion->getAsunto()) }}"
                            data-mensaje="{!! e($notificacion->getMensaje()) !!}">
                            <div>
                                <p class="text-gray-700">{{ $notificacion->getAsunto() }}</p>
                                <p class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($notificacion->getFechaCreacion())->format('d/m/Y') }}</p>
                            </div>
                            <i class="fas fa-check-double text-sm text-gray-400"></i>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>


    {{-- Modal --}}
    <div id="modal-notificacion" class="fixed inset-0 bg-black bg-opacity-30 hidden justify-center items-center z-50">
        <div id="modal-contenedor"
            class="bg-white w-full max-w-4xl p-6 rounded-lg shadow-lg border relative max-h-[80vh] overflow-y-auto transition-all duration-300">
            <button onclick="cerrarModal()" class="absolute top-3 right-3 text-gray-500 hover:text-red-600 transition text-lg">
                <i class="fas fa-times"></i>
            </button>
            <h4 id="modal-titulo" class="text-lg font-semibold mb-3 text-gray-800 pr-6"></h4>
            <p id="modal-mensaje" class="text-sm text-gray-700 leading-relaxed mb-4 text-justify"></p>

            <div class="flex justify-between text-xs text-blue-600 mt-2">
                <button onclick="navegarNotificacion(-1)" class="hover:underline">← Anterior</button>
                <button onclick="navegarNotificacion(1)" class="hover:underline">Siguiente →</button>
            </div>
        </div>
    </div>


    @endsection
    
    
</div>

    {{-- Modal estudiantes vinculados --}}

    @include('programa_academico.procesos.estudiantes')

@endsection


@section('scripts')
<script>
    let notificaciones = [];
    let indiceActual = 0;

    document.addEventListener("DOMContentLoaded", () => {
        const lista = [...document.querySelectorAll("#lista-no-leidas li, #lista-leidas li")];
        notificaciones = lista;
    });

    function mostrarContenido(li) {
        const titulo = li.getAttribute("data-titulo");
        const mensaje = li.getAttribute("data-mensaje");
        const contenedor = document.getElementById("modal-contenedor");

        // Ancho dinámico según longitud del mensaje
        const longitud = mensaje.length;
        contenedor.classList.remove("max-w-md", "max-w-xl", "max-w-3xl");
        if (longitud < 300) contenedor.classList.add("max-w-md");
        else if (longitud < 800) contenedor.classList.add("max-w-xl");
        else contenedor.classList.add("max-w-3xl");

        // Actualiza contenido
        document.getElementById("modal-titulo").textContent = titulo;
        document.getElementById("modal-mensaje").innerHTML = mensaje.replace(/\n/g, "<br>");

        // Mostrar modal
        document.getElementById("modal-notificacion").classList.remove("hidden");
        document.getElementById("modal-notificacion").classList.add("flex");

        // Índice actual
        indiceActual = notificaciones.findIndex(item => item === li);
    }

    function cerrarModal() {
        document.getElementById("modal-notificacion").classList.add("hidden");
        document.getElementById("modal-notificacion").classList.remove("flex");
    }

    function navegarNotificacion(direccion) {
        const nuevoIndice = indiceActual + direccion;
        if (nuevoIndice >= 0 && nuevoIndice < notificaciones.length) {
            mostrarContenido(notificaciones[nuevoIndice]);
        }
    }

    function marcarComoLeida(event, boton) {
        event.stopPropagation();

        const item = boton.closest("li");
        const notiId = item.getAttribute("data-id"); // Asegúrate de tener esto en tu li

        // Eliminar botón y mover ítem a la lista de leídas
        item.querySelector("button").remove();
        const checkIcon = document.createElement("i");
        checkIcon.className = "fas fa-check-double text-sm text-gray-400";
        item.appendChild(checkIcon);
        document.getElementById("lista-leidas").appendChild(item);

        // Ocultar mensaje si ya no hay no leídas
        if (document.querySelectorAll("#lista-no-leidas li").length === 0) {
            document.getElementById("sin-recientes").classList.remove("hidden");
        }

        // Llamada al backend
        fetch(`/notificaciones/${notiId}/marcar-leida`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        }).then(response => {
            if (!response.ok) {
                console.error('Error al marcar como leída');
            }
        });
    }
</script>
@endsection
