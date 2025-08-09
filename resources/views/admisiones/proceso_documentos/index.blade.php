@extends('layouts.app')

@section('title', 'Documentos del Proceso')

@section('header', 'Documentos del Proceso')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
    
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            Documentos del Proceso: {{ $proceso->getNombre() }}
        </h2>

        <!-- Botón Adjuntar Documento -->
        <a href="{{ route('proceso_documentos.create', $proceso->getId()) }}" 
           class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
            + Adjuntar Documento
        </a>
    </div>

    <!-- Tabla de Documentos -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
            <thead>
                <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                    <th class="px-4 py-2 font-medium w-3/5">Nombre del Documento</th>
                    <th class="px-4 py-2 font-medium w-1/5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proceso->getDocumentos() as $documento)
                    <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                        <td class="px-4 py-2 text-gray-900 break-words">
                            {{ $documento->getNombre() }}
                        </td>
                        <td class="px-4 py-2 text-center flex justify-center gap-4">

                            <!-- Botón Ver/Descargar -->
                            <a href="{{ asset($documento->getRuta()) }}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800 transition">
                                Ver
                            </a>

                            <!-- Botón Eliminar -->
                            <form action="{{ route('proceso_documentos.destroy', [$proceso->getId(), $documento->getId()]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        class="eliminar-btn text-red-600 hover:text-red-800 transition" 
                                        data-url="{{ route('proceso_documentos.destroy', [$proceso->getId(), $documento->getId()]) }}">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-4 text-gray-500">
                            No se encontraron documentos adjuntos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
            text: "Esta acción eliminará el documento adjunto.",
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
