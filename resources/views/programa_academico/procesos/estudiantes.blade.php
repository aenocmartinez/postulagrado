<!-- Modal Gestión Estudiantes -->
<div id="modal-gestion-estudiantes"
     class="fixed inset-0 bg-black bg-opacity-30 hidden justify-center items-start z-[9999] pt-10">
    <div class="bg-white max-w-7xl w-full mx-auto rounded-lg shadow-lg p-6 relative max-h-[90vh] overflow-y-auto z-[10000]">

        <button onclick="cerrarModalGestionEstudiantes()" class="absolute top-3 right-4 text-gray-500 hover:text-red-600">
            <i class="fas fa-times text-lg"></i>
        </button>

        <h3 class="text-lg font-semibold text-gray-800 mb-4">Buscar estudiantes candidatos a grado</h3>

        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
            <div>
                <label for="anio" class="block mb-1 font-medium">Año</label>
                <select id="anio" class="w-full border-gray-300 rounded text-sm">
                    <option>2025</option>
                    <option>2024</option>
                    <option>2023</option>
                </select>
            </div>
            <div>
                <label for="periodo" class="block mb-1 font-medium">Periodo</label>
                <select id="periodo" class="w-full border-gray-300 rounded text-sm">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div>
                <label for="tipo" class="block mb-1 font-medium">Tipo</label>
                <select id="tipo" class="w-full border-gray-300 rounded text-sm">
                    <option value="semestral">Semestral</option>
                    <option value="trimestral">Trimestral</option>
                </select>
            </div>
        </div>

        <button onclick="cargarListadoEstudiantes()" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 mb-6">
            Buscar Estudiantes
        </button>

        <!-- Loading estudiantes -->
        <div id="loader-estudiantes" class="hidden flex items-center justify-center py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-sm text-gray-600">Cargando estudiantes...</span>
        </div>


        <div id="tabla-estudiantes" class="hidden">
            <h4 class="text-md font-semibold text-gray-700 mb-2">Estudiantes Encontrados</h4>

                <!-- Encabezado contextual mejorado -->
                <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded-md mb-4 text-sm text-gray-700 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-blue-600"></i>
                            <p><strong>Programa:</strong> Tecnología en Gestión Ambiental</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-600"></i>
                            <p><strong>Créditos del Pensum:</strong> 120</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users text-blue-600"></i>
                            <p><strong>Estudiantes encontrados:</strong> 15</p>
                        </div>
                    </div>
                </div>

            <!-- <div class="overflow-x-auto border rounded-lg"> -->


                <!-- Buscador en la tabla -->
                <div class="mb-2 mt-4 flex items-center justify-end">
                    <input
                        type="text"
                        id="filtro-estudiantes"
                        placeholder="Buscar por nombre, documento o código..."
                        class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        oninput="filtrarEstudiantes()"
                    >
                </div>

                <!-- Tabla estilizada -->
                <div class="overflow-x-auto border rounded-lg">

                    <table class="min-w-full text-sm text-left border-collapse">
                        <thead class="bg-blue-100 text-blue-900 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3">Pensum</th>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Documento</th>
                                <th class="px-4 py-3">Nombre</th>
                                <th class="px-4 py-3">Ubicación</th>
                                <th class="px-4 py-3">Situación</th>
                                <th class="px-2 py-3 whitespace-nowrap text-center">Créditos<br>Pend.</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">
                            @for ($i = 1; $i <= 15; $i++)
                                <tr class="{{ $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition">
                                    <td class="px-4 py-2">{{ rand(2018, 2020) }}</td>
                                    <td class="px-4 py-2">2025{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-2">{{ 1000000000 + rand(10000000, 99999999) }}</td>
                                    <td class="px-4 py-2">Estudiante {{ $i }}</td>
                                    <td class="px-4 py-2">{{ rand(5, 8) }}° semestre</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $estado = ['Activa', 'Inactiva', 'Pendiente'][rand(0, 2)];
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-full text-xs 
                                            {{ $estado === 'Activa' ? 'bg-green-100 text-green-700' : ($estado === 'Inactiva' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ $estado }}
                                        </span>
                                    </td>
                                    @php $pend = rand(0, 20); @endphp
                                    <td class="px-2 py-2 text-center">{{ $pend }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex items-center justify-center gap-3 text-xs">
                                            <button class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            <button class="text-red-500 hover:text-red-700 flex items-center gap-1">
                                                <i class="fas fa-trash-alt"></i> Quitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

            <!-- </div> -->

        </div>

        <div id="mensaje-sin-estudiantes" class="hidden text-center py-10 text-gray-500 text-sm">
            <i class="fas fa-user-slash text-4xl text-gray-400 mb-4"></i>
            <p>No se encontraron estudiantes para los criterios seleccionados.</p>
            <p>Verifica que el año, periodo y tipo sean correctos.</p>
        </div>
    </div>
</div>

<script>
    function abrirModalGestionEstudiantes() {
        document.getElementById('modal-gestion-estudiantes').classList.remove('hidden');
        document.getElementById('modal-gestion-estudiantes').classList.add('flex');
        document.getElementById('tabla-estudiantes').classList.add('hidden');
    }

    function cerrarModalGestionEstudiantes() {
        document.getElementById('modal-gestion-estudiantes').classList.add('hidden');
        document.getElementById('modal-gestion-estudiantes').classList.remove('flex');
    }

    function cargarListadoEstudiantes() {
        const tabla = document.getElementById('tabla-estudiantes');
        const mensaje = document.getElementById('mensaje-sin-estudiantes');
        const loader = document.getElementById('loader-estudiantes');

        // Oculta todo
        tabla.classList.add('hidden');
        mensaje.classList.add('hidden');
        loader.classList.remove('hidden');

        // Simula carga (puedes reemplazar con llamada AJAX real)
        setTimeout(() => {
            loader.classList.add('hidden');

            const tieneResultados = Math.random() > 0.3; // Simulación

            if (tieneResultados) {
                tabla.classList.remove('hidden');
            } else {
                mensaje.classList.remove('hidden');
            }
        }, 1500);
    }

    function filtrarEstudiantes() {
        const filtro = document.getElementById("filtro-estudiantes").value.toLowerCase();
        const filas = document.querySelectorAll("#tabla-estudiantes tbody tr");

        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? "" : "none";
        });
    }

</script>
