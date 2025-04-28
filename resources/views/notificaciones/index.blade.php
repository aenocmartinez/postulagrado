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

            <!-- Botón Nueva Notificación -->
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
                    <th class="px-4 py-2 font-medium w-[30%]">Asunto</th>
                    <th class="px-4 py-2 font-medium w-[30%]">Canal</th>
                    <th class="px-4 py-2 font-medium w-[30%]">Fecha de Envío</th>
                    <th class="px-4 py-2 font-medium text-center w-[10%]">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notificaciones as $notificacion)
                    <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                        <td class="px-4 py-2 text-gray-900 break-words">{{ $notificacion->getAsunto() }}</td>
                        <td class="px-4 py-2 text-gray-900 break-words">{{ ucfirst($notificacion->getCanal()) }}</td>
                        <td class="px-4 py-2 text-gray-900 break-words">{{ \Carbon\Carbon::parse($notificacion->getFechaCreacion())->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-center">
                            <button class="menu-btn text-gray-600 hover:text-gray-800"
                                    data-id="{{ $notificacion->getId() }}">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
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
    <a id="view-link" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver</a>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const actionMenu = document.getElementById("action-menu");
    const viewLink = document.getElementById("view-link");
    const editLink = document.getElementById("edit-link");
    const deleteBtn = document.getElementById("delete-btn");

    document.querySelectorAll(".menu-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();

            let rect = button.getBoundingClientRect();
            let notificacionId = button.dataset.id;

            actionMenu.style.top = `${rect.bottom + window.scrollY}px`;
            actionMenu.style.left = `${rect.left}px`;
            actionMenu.classList.remove("hidden");

            viewLink.href = `{{ route('notificaciones.show', ':id') }}`.replace(':id', notificacionId);
        });
    });

    document.addEventListener("click", function () {
        actionMenu.classList.add("hidden");
    });

    deleteBtn.addEventListener("click", function () {
        let url = deleteBtn.dataset.url;

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
    });
});
</script>
@endsection
