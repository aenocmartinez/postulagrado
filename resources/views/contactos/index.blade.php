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
                    <input type="text" name="search" 
                        placeholder="Buscar contacto..." 
                        value="{{ request('search') }}"
                        class="border border-gray-300 px-3 py-2 rounded-l-md text-sm w-64 focus:ring focus:ring-gray-400 outline-none">

                    <button type="submit" class="bg-gray-700 px-4 py-2 rounded-r-md hover:bg-gray-800 transition flex items-center justify-center" aria-label="Buscar">
                        <i class="fas fa-magnifying-glass text-gray-300 text-sm opacity-75"></i>
                    </button>
                </form>

                <!-- Botón Nuevo Contacto -->
                <a href="{{ route('contactos.create') }}" 
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                    + Nuevo Contacto
                </a>
            </div>

        </div>

        <!-- Tabla de contactos -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium">Nombre</th>
                        <th class="px-4 py-2 font-medium">Email</th>
                        <th class="px-4 py-2 font-medium">Teléfono</th>
                        <th class="px-4 py-2 font-medium">Programa</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contactos as $contacto)
                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-900">{{ $contacto->getNombre() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $contacto->getEmail() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $contacto->getTelefono() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $contacto->getPrograma()->getNombre() }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <!-- Botón Editar -->
                                    <a href="{{ route('contactos.edit', $contacto->getId()) }}" 
                                       class="hover:text-blue-600 transition">
                                        Editar
                                    </a>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('contactos.destroy', $contacto->getId()) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="eliminar-btn hover:text-red-600 transition" 
                                                data-url="{{ route('contactos.destroy', $contacto->getId()) }}">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                No se encontraron contactos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <div class="mt-4">
            {{ $contactos->appends(['search' => request('search')])->links() }}
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".eliminar-btn").forEach((button) => {
            button.addEventListener("click", function () {
                let url = this.dataset.url;
                confirmarEliminacion(url);
            });
        });
    });

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
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
