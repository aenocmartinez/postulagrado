<!-- Modal para estudiantes asociados -->
<div id="modal-estudiantes-asociados" class="fixed inset-0 bg-black bg-opacity-30 hidden justify-center items-center z-50">
    <div class="bg-white max-w-5xl w-full p-6 rounded shadow-lg overflow-y-auto max-h-[80vh] relative">
        <button onclick="cerrarModalEstudiantesAsociados()" class="absolute top-3 right-3 text-gray-500 hover:text-red-600 transition text-lg">
            <i class="fas fa-times"></i>
        </button>

        <h3 class="text-lg font-semibold text-gray-800 mb-4">Estudiantes Asociados</h3>

        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Nombre</th>
                    <th class="p-2 border">Documento</th>
                    <th class="p-2 border">Correo</th>
                    <th class="p-2 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiantes as $index => $estudiante)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border">{{ $index + 1 }}</td>
                        <td class="p-2 border">{{ $estudiante->getNombreCompleto() }}</td>
                        <td class="p-2 border">{{ $estudiante->getDocumento() }}</td>
                        <td class="p-2 border">{{ $estudiante->getCorreo() }}</td>
                        <td class="p-2 border">
                            <button onclick="verDetalleEstudiante('{{ $estudiante->getId() }}')" class="text-blue-600 hover:underline">Ver</button>
                            |
                            <button onclick="quitarEstudiante('{{ $estudiante->getId() }}')" class="text-red-600 hover:underline">Quitar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4 text-gray-500">No hay estudiantes asociados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
