@extends('layouts.app')

@section('title', 'Tablero de Seguimiento')

@section('header', 'Seguimiento del Proceso')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-6xl mx-auto">
    
    <!-- 📌 Información del Proceso -->
    <div class="mb-6 flex justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Proceso: {{ $proceso->getNombre() }}</h2>
            <p class="text-sm text-gray-600">Nivel Educativo: <strong>{{ $proceso->getNivelEducativo() }}</strong></p>
            <p class="text-sm text-gray-600">Estado: 
                <span class="text-green-600">{{ $proceso->getEstado() }}</span>
            </p>
        </div>
    </div>

    <!-- 🔴🟡🟢 Segmentación de Actividades con Listado -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Actividades</h3>
    <div class="grid grid-cols-2 gap-6">
    @php
        $actividades = [
            'Finalizadas' => ['color' => 'gray-200', 'text' => 'text-gray-800', 'items' => []], 
            'En Curso' => ['color' => 'green-200', 'text' => 'text-green-800', 'items' => []], 
            'Programadas' => ['color' => 'yellow-200', 'text' => 'text-yellow-800', 'items' => []], 
            'Próximas a Iniciar' => ['color' => 'orange-200', 'text' => 'text-orange-800', 'items' => []],
        ];

        foreach($proceso->getActividadesPorEstadoTemporal() as $index => $actividad) {

            if ($index == "EnCurso") {
                foreach($actividad as $item) {
                    $actividades['En Curso']['items'][] = [
                        $item->getDescripcion(),
                        $item->getFechaInicio(),
                        $item->getFechaFin(),
                    ];
                }
                continue;
            }

            if ($index == "Finalizadas") {
                foreach($actividad as $item) {
                    $actividades['Finalizadas']['items'][] = [
                        $item->getDescripcion(),
                        $item->getFechaInicio(),
                        $item->getFechaFin(),
                    ];
                }
                continue;
            }

            if ($index == "Programadas") {
                foreach($actividad as $item) {
                    $actividades['Programadas']['items'][] = [
                        $item->getDescripcion(),
                        $item->getFechaInicio(),
                        $item->getFechaFin(),
                    ];
                }
                continue;
            } 

            if ($index == "ProximasIniciar") {
                foreach($actividad as $item) {
                    $actividades['Próximas a Iniciar']['items'][] = [
                        $item->getDescripcion(),
                        $item->getFechaInicio(),
                        $item->getFechaFin(),
                    ];
                }
                continue;
            }                
        }
    @endphp

        @foreach ($actividades as $titulo => $data)
        <div class="bg-{{ $data['color'] }} p-4 rounded-lg">
            <h4 class="text-sm font-semibold {{ $data['text'] }} mb-2 cursor-pointer" onclick="toggleLista('{{ strtolower(str_replace(' ', '_', $titulo)) }}')">
                🔹 {{ $titulo }} <span class="text-xs text-gray-500">(Ver más)</span>
            </h4>
            <div id="{{ strtolower(str_replace(' ', '_', $titulo)) }}" class="hidden">
                <ul class="list-disc pl-4">
                    @foreach ($data['items'] as $actividad)
                        <li class="text-xs {{ $data['text'] }}">{{ $actividad[0] }} ({{ $actividad[1] }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>    

    <!-- 📚 Avance de Programas Académicos (Listado en Tabla) -->       
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Avance por Programa Académico</h3>

<!-- Buscador -->
<div class="flex justify-between items-center mb-4">
    <input type="text" id="buscar-programa" placeholder="Buscar programa..." 
           class="border border-gray-300 px-3 py-2 rounded-md text-sm w-64 focus:ring focus:ring-gray-400 outline-none">

    <!-- <button id="ver-todos" 
            class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 transition">
        Ver Todos
    </button> -->
</div>

<!-- Tabla de Programas -->
<div class="overflow-x-auto">
    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
        <thead>
            <tr class="bg-gray-200 text-gray-700 text-left">
                <th class="px-4 py-2 font-medium">Programa Académico</th>
                <th class="px-4 py-2 font-medium text-center">Avance</th>
                <th class="px-4 py-2 font-medium text-center">Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-programas">
            @foreach ($proceso->getProgramas() as $index => $programaProceso)
            <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition 
                       {{ $index >= 10 ? 'programa-oculto hidden' : '' }}">
                <!-- Nombre con Tooltip -->
                <td class="px-4 py-2 text-gray-900 relative group">
                    <span class="cursor-pointer tooltip" 
                          data-info="Unidad Regional: {{ $programaProceso->getPrograma()->getUnidadRegional()->getNombre() }}">
                        {{ $programaProceso->getPrograma()->getNombre() }}
                    </span>
                </td>

                <!-- Avance -->
                <td class="px-4 py-2 text-center">
                    <div class="w-full bg-gray-300 rounded h-2">
                        <div class="h-2 bg-blue-500 rounded" style="width: {{ $programaProceso->getPorcentajeAvance() }}%"></div>
                    </div>
                    <span class="text-xs text-gray-600">{{ $programaProceso->getPorcentajeAvance() }}%</span>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-2 text-center">
                    <button class="text-sm px-3 py-1 rounded border border-gray-400 text-gray-700 hover:bg-gray-100 transition btn-omitir"
                        data-id="{{ $programaProceso->getPrograma()->getId() }}">
                        Omitir
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Paginación -->
<div class="mt-4 flex justify-center">
    <button id="cargar-mas"
            class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 transition">
        Cargar Más
    </button>
</div>



    <!-- 📊 Paz y Salvo - Financiera y Biblioteca -->
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
        <div class="bg-gray-200 p-4 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">🏛️ {{ $area }}</h4>
            <p class="text-xs text-gray-600">Total de Estudiantes: <strong>{{ $datos['total'] }}</strong></p>
            <p class="text-xs text-gray-600">Pendientes: <strong class="text-red-600">{{ $datos['pendientes'] }}</strong></p>
            <div class="w-full bg-gray-300 rounded mt-2">
                <div class="h-2 bg-blue-500 rounded" style="width: {{ (1 - $datos['pendientes'] / $datos['total']) * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>    

    <!-- 📩 Notificaciones -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Notificaciones Enviadas</h3>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
            <thead>
                <tr class="bg-gray-200 text-gray-700 text-left">
                    <th class="px-4 py-2 font-medium">Notificación</th>
                    <th class="px-4 py-2 font-medium">Destinatario</th>
                    <th class="px-4 py-2 font-medium">Fecha de Envío</th>
                    <th class="px-4 py-2 font-medium">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach([['Validación de documentos', 'Ana Gómez', '2024-02-10', false], ['Firma de resolución', 'Carlos Rodríguez', '2024-02-12', true]] as [$mensaje, $usuario, $fecha, $leido])
                <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                    <td class="px-4 py-2 text-gray-900">{{ $mensaje }}</td>
                    <td class="px-4 py-2 text-gray-900">{{ $usuario }}</td>
                    <td class="px-4 py-2 text-gray-900">{{ $fecha }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $leido ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $leido ? 'Leído' : 'Pendiente' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')

<!-- ✅ Importar jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $(document).ready(function () {
        inicializarEventos();
    });

    function inicializarEventos() {
        $("#buscar-programa").on("input", filtrarProgramas);
        $("#cargar-mas").on("click", cargarMasProgramas);
        $("#ver-todos").on("click", mostrarTodosLosProgramas); // 🟢 Asegurar que esta función existe
        actualizarEventosTooltips();
        actualizarEventosOmitir();
    }

    // ✅ 🔍 FILTRAR PROGRAMAS (Soporte para mayúsculas y tildes)
    function filtrarProgramas() {
        let input = $("#buscar-programa").val().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
        let hayResultados = false;

        console.log(input)

        $("#tabla-programas tr").each(function () {

            let nombrePrograma = $(this).find(".tooltip").text().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();

            if (nombrePrograma.includes(input)) {
                $(this).removeClass("hidden").show();
                hayResultados = true;
            } else {
                $(this).addClass("hidden").hide();
            }
        });

        if (input === "") {
            //mostrarTodosLosProgramas(); // 🟢 Restablece la tabla si no hay texto
            $("#tabla-programas tr").each(function (index) {
                if (index < 10) {
                    $(this).removeClass("hidden").show();
                } else {
                    $(this).addClass("hidden").hide();
                }
            });

            $("#no-resultados").remove();
            $("#cargar-mas").show(); // 🟢 Volver a mostrar el botón de paginación si había desaparecido
            return; // ⬅️ Detener aquí la función
        }

        if (!hayResultados) {
            if ($("#no-resultados").length === 0) {
                $("#tabla-programas tbody").append(`
                    <tr id="no-resultados">
                        <td colspan="3" class="text-center text-gray-500">No se encontraron programas</td>
                    </tr>`);
            }
        } else {
            $("#no-resultados").remove();
        }

        actualizarEventosTooltips();
    }

    // ✅ MOSTRAR TODOS LOS PROGRAMAS (Corrige error)
    function mostrarTodosLosProgramas() {
        $(".programa-oculto").removeClass("hidden").show();
        $("#cargar-mas").hide();
    }

    // ✅ TOOLTIP MEJORADO
    function actualizarEventosTooltips() {
        $(".tooltip").off("mouseenter mouseleave").hover(function () {
            let info = $(this).data("info");
            let tooltip = $("<div>")
                .addClass("tooltip-box absolute bg-gray-800 text-white text-xs rounded p-2 shadow-lg")
                .text(info)
                .appendTo("body");

            let rect = this.getBoundingClientRect();
            tooltip.css({
                position: "absolute",
                top: rect.top + window.scrollY - tooltip.outerHeight() - 5 + "px",
                left: rect.left + window.scrollX + "px",
                zIndex: "9999",
                whiteSpace: "nowrap",
                padding: "6px 10px"
            });

            $(this).data("tooltip-element", tooltip);
        }, function () {
            let tooltip = $(this).data("tooltip-element");
            if (tooltip) {
                tooltip.remove();
                $(this).removeData("tooltip-element");
            }
        });
    }

    // ✅ CARGAR MÁS PROGRAMAS
    function cargarMasProgramas() {
        let programasOcultos = $(".programa-oculto:hidden").slice(0, 10);
        programasOcultos.removeClass("programa-oculto hidden").fadeIn();

        if ($(".programa-oculto:hidden").length === 0) {
            $("#cargar-mas").hide();
        }

        actualizarEventosTooltips();
    }

    // ✅ OMISIÓN DE PROGRAMAS
    function actualizarEventosOmitir() {
        $(".btn-omitir").on("click", function () {
            toggleOmitirPrograma($(this).data("id"));
        });
    }

    function toggleOmitirPrograma(programaId) {
        let boton = $(`button[data-id="${programaId}"]`);
        let procesoId = "{{ $proceso->getId() }}";
        let url = `/procesos/${procesoId}/programas/${programaId}`;

        if (typeof Swal === "undefined") {
            console.error("SweetAlert2 no está cargado. Verifica que el CDN está incluido.");
            return;
        }

        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción removerá el programa del proceso.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Protección CSRF
                    },
                    success: function () {
                        // ✅ Elimina la fila del programa
                        let fila = boton.closest("tr");
                        fila.fadeOut(300, function () {
                            $(this).remove();
                            actualizarListaProgramas(); // Llama a la función para cargar más programas
                        });
                    },
                    error: function () {
                        Swal.fire("Error", "No se pudo eliminar el programa.", "error");
                    },
                });
            }
        });
    }

    function actualizarListaProgramas() {
        let programasVisibles = $("#tabla-programas tbody tr:visible").length;
        let programasOcultos = $(".programa-oculto:hidden");

        // Si hay menos de 10 programas visibles, mostrar más hasta completar 10
        while (programasVisibles < 10 && programasOcultos.length > 0) {
            $(programasOcultos[0]).removeClass("programa-oculto hidden").fadeIn();
            programasVisibles++;
            programasOcultos = $(".programa-oculto:hidden"); // Actualiza la lista de ocultos
        }

        // Si ya no hay más programas ocultos, esconde el botón de "Cargar Más"
        if (programasOcultos.length === 0) {
            $("#cargar-mas").hide();
        }
    }

    // ✅ Mantener función toggleLista
    function toggleLista(id) {
        let element = document.getElementById(id);
        element.classList.toggle('hidden');
    }

</script>

@endsection

