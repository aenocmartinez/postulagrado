@extends('layouts.app')

@section('title', 'Listado de Contactos de Programas')

@section('header', 'Listado de Contactos de Programas')

@section('content')

    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
        
        <!-- Buscador y botón de nuevo contacto -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Contactos de Programas</h2>
            
            <div class="flex space-x-2">
                <!-- Formulario de búsqueda -->
                <form method="GET" action="{{ route('contactos.index') }}" class="flex">
                    <input type="text" name="criterio" 
                        placeholder="Buscar contacto..." 
                        value="{{ request('criterio') }}"
                        class="border border-gray-300 px-3 py-2 rounded-l-md text-sm w-64 focus:ring focus:ring-gray-400 outline-none">

                    <button type="submit" class="bg-gray-700 px-4 py-2 rounded-r-md hover:bg-gray-800 transition flex items-center justify-center" aria-label="Buscar">
                        <i class="fas fa-magnifying-glass text-gray-300 text-sm opacity-75"></i>
                    </button>
                </form>

                <!-- Botón Nuevo Contacto -->
                <a href="{{ route('contactos.create') }}" 
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                    + Nuevo contacto
                </a>
            </div>

        </div>

        <!-- Tabla de contactos -->
        <div class="overflow-hidden">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium w-[30%]">Nombre</th>
                        <th class="px-4 py-2 font-medium w-[30%]">Email</th>
                        <th class="px-4 py-2 font-medium w-[30%]">Programa</th>
                        <th class="px-4 py-2 font-medium text-center w-[10%]">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contactos as $contacto)
                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-900 break-words">{{ $contacto['nombre'] }}</td>
                            <td class="px-4 py-2 text-gray-900 break-words">{{ $contacto['email'] }}</td>
                            <td class="px-4 py-2 text-gray-900 break-words">{{ $contacto['programaNombre'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <button class="menu-btn text-gray-600 hover:text-gray-800"
                                        data-id="{{ $contacto['id'] }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">
                                No se encontraron contactos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <div class="mt-4">
            
        </div>
    </div>

    <!-- Menú flotante global (Se mueve dinámicamente) -->
    <div id="action-menu" class="hidden fixed bg-white shadow-lg rounded-md w-32 border border-gray-200 z-50">
        <a id="view-link" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver datos</a>
        <a id="edit-link" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar</a>
        <button id="delete-btn" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
            Eliminar
        </button>
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
            
            // Obtener posición del botón y el ID del contacto
            let rect = button.getBoundingClientRect();
            let contactoId = button.dataset.id;

            // Ajustar posición del menú en la pantalla
            actionMenu.style.top = `${rect.bottom + window.scrollY}px`;
            actionMenu.style.left = `${rect.left}px`;
            actionMenu.classList.remove("hidden");

            // Asignar rutas correctas al menú de edición, eliminación y ver info
            viewLink.href = `{{ route('contactos.show', ':id') }}`.replace(':id', contactoId);
            editLink.href = `{{ route('contactos.edit', ':id') }}`.replace(':id', contactoId);
            deleteBtn.dataset.url = `{{ route('contactos.destroy', ':id') }}`.replace(':id', contactoId);
        });
    });

    // Ocultar menú si se hace clic fuera
    document.addEventListener("click", function () {
        actionMenu.classList.add("hidden");
    });

    // Confirmar eliminación con SweetAlert
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
