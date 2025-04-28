@extends('layouts.app')

@section('title', 'Listado de Notificaciones')

@section('header', 'Listado de Notificaciones')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
    
    <!-- Buscador y botón de nueva notificación -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Notificaciones</h2>
        
        <div class="flex space-x-2">
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('notificaciones.index') }}" class="flex">
                <input type="text" name="criterio" 
                    placeholder="Buscar notificación..." 
                    value="{{ request('criterio') }}"
                    class="border border-gray-300 px-3 py-2 rounded-l-md text-sm w-64 focus:ring focus:ring-gray-400 outline-none">

                <button type="submit" class="bg-gray-700 px-4 py-2 rounded-r-md hover:bg-gray-800 transition flex items-center justify-center" aria-label="Buscar">
                    <i class="fas fa-magnifying-glass text-gray-300 text-sm opacity-75"></i>
                </button>
            </form>

            <a href="{{ route('notificaciones.create') }}" 
               class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                + Nueva Notificación
            </a>
        </div>

    </div>

    <!-- Tabla de notificaciones -->
    <div class="overflow-hidden">
        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
            <thead>
                <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                    <th class="px-4 py-2 font-medium w-[25%]">Asunto</th>
                    <th class="px-4 py-2 font-medium w-[20%]">Canal</th>
                    <th class="px-4 py-2 font-medium w-[20%]">Fecha de Envío</th>
                    <th class="px-4 py-2 font-medium w-[20%]">Estado</th>
                    <th class="px-4 py-2 font-medium text-center w-[15%]">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notificaciones as $notificacion)
                    @php
                        $estado = strtoupper($notificacion->getEstado() ?? '');
                        $clase = match($estado) {
                            'PROGRAMADA' => 'text-yellow-500',
                            'ENVIADA' => 'text-green-600',
                            'ANULADA' => 'text-red-600',
                            default => 'text-gray-600',
                        };
                    @endphp
                    <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                        <td class="px-4 py-2 text-gray-900 break-words">{{ $notificacion->getAsunto() }}</td>
                        <td class="px-4 py-2 text-gray-900 break-words">{{ $notificacion->getCanal() }}</td>
                        <td class="px-4 py-2 text-gray-900 break-words">
                            {{ \Carbon\Carbon::parse($notificacion->getFechaCreacion())->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2 text-gray-900 break-words">
                            <span class="font-semibold {{ $clase }}">
                                {{ $estado !== '' ? $estado : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button class="menu-btn text-gray-600 hover:text-gray-800"
                                    data-id="{{ $notificacion->getId() }}"
                                    data-estado="{{ $estado }}">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No se encontraron notificaciones.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginador -->
    <div class="mt-4">
        {{ $notificaciones->appends(['criterio' => request('criterio')])->links() }}
    </div>

</div>

<!-- Menú flotante de acciones -->
<div id="action-menu" class="hidden fixed bg-white shadow-lg rounded-md w-32 border border-gray-200 z-50">
    <a id="view-link" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver más</a>
    <form id="anular-form" method="POST" action="#" class="block">
        @csrf
        @method('PATCH')
        <button type="submit" id="anular-btn" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
            Anular
        </button>
    </form>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const actionMenu = document.getElementById("action-menu");
    const viewLink = document.getElementById("view-link");
    const anularForm = document.getElementById("anular-form");
    const anularBtn = document.getElementById("anular-btn");

    document.querySelectorAll(".menu-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();

            let rect = button.getBoundingClientRect();
            let notificacionId = button.dataset.id;
            let estado = button.dataset.estado;

            actionMenu.style.top = `${rect.bottom + window.scrollY}px`;
            actionMenu.style.left = `${rect.left}px`;
            actionMenu.classList.remove("hidden");

            viewLink.href = `{{ route('notificaciones.show', ':id') }}`.replace(':id', notificacionId);
            
            if (estado === "PROGRAMADA") {
                anularForm.action = `{{ route('notificaciones.anular', ':id') }}`.replace(':id', notificacionId);
                anularForm.classList.remove('hidden');
            } else {
                anularForm.classList.add('hidden');
            }
        });
    });

    document.addEventListener("click", function () {
        actionMenu.classList.add("hidden");
    });
});
</script>
@endsection
