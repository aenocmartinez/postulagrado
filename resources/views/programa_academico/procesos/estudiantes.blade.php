<!-- Modal Para Buscar Estudiantes Por Primera vez -->
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
                            <p><strong>Programa:</strong> {{ auth()->user()->programaAcademico()->getNombre() }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-600"></i>
                            <p><strong>Créditos del Pensum:</strong> <span id="creditos-pensum">120</span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users text-blue-600"></i>
                            <p><strong>Estudiantes encontrados:</strong> <span id="estudiantes-encontrados">0</span></p>
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
                                <th class="px-4 py-3 text-center">Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">

                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-end">
                        <button onclick="guardarSeleccionados()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm mb-2">
                            Guardar seleccionados
                        </button>
                    </div>

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

<!-- Modal ver estudiantes vinculados -->
<div id="modal-estudiantes-vinculados"
     class="fixed inset-0 bg-black bg-opacity-30 hidden justify-center items-start z-[9999] pt-10">
    <div class="bg-white max-w-7xl w-full mx-auto rounded-lg shadow-lg p-6 relative max-h-[90vh] overflow-y-auto z-[10000]">

        <button onclick="cerrarModalEstudiantesVinculados()" class="absolute top-3 right-4 text-gray-500 hover:text-red-600">
            <i class="fas fa-times text-lg"></i>
        </button>

        <h3 class="text-lg font-semibold text-gray-800 mb-4">Estudiantes vinculados al proceso</h3>

        <!-- Encabezado contextual -->
        <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-md mb-4 text-sm text-gray-700 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-green-600"></i>
                    <p><strong>Programa:</strong> {{ auth()->user()->programaAcademico()->getNombre() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-id-badge text-green-600"></i>
                    <p><strong>Código SNIES:</strong> {{ auth()->user()->programaAcademico()->getSnies() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-green-600"></i>
                    <p><strong>Total vinculados:</strong> {{ count(auth()->user()->programaAcademico()->listarEstudiantesCandidatos($proceso->getId())) }}</p>
                </div>
            </div>
        </div>

        <!-- Tabla de estudiantes que hacen parte del proceso -->
        <div class="overflow-x-auto border rounded-lg">

            <!-- Buscador en la tabla -->
            <div class="flex justify-end mb-4 mt-2 mr-2">
                <input type="text"
                    id="buscador-estudiantes"
                    placeholder="Buscar por nombre, código o documento"
                    class="border border-gray-300 rounded px-4 py-2 text-sm w-full md:w-1/2 lg:w-1/3 focus:outline-none focus:ring-2 focus:ring-green-400"
                >
            </div>

            <!-- Tabla de estudiantes vinculados al proceso -->
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-green-100 text-green-900 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">Pensum</th>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Documento</th>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Ubicación</th>
                        <th class="px-4 py-3">Situación</th>
                        <th class="px-2 py-3 text-center whitespace-nowrap">Créditos<br>Pend.</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @foreach(auth()->user()->programaAcademico()->listarEstudiantesCandidatos($proceso->getId()) as $est)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $est['detalle']->pensum_estud ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $est['estu_codigo'] }}</td>
                            <td class="px-4 py-2">{{ $est['detalle']->documento ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $est['detalle']->nombres ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $est['detalle']->ubicacion_semestral ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                    {{ $est['detalle']->situacion ?? '-' }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center">{{ $est['detalle']->cred_pendientes ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Botón Ver -->
                                    <button onclick="verDetalleEstudiante('{{ $est['estu_codigo'] }}')"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                            title="Ver detalles del estudiante">
                                        <i class="fas fa-search mr-1"></i> Ver
                                    </button>
                                    <!-- Botón Quitar -->
                                    <button onclick="quitarEstudianteDelProceso('{{ $est['ppes_id'] }}')"
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400"
                                            title="Quitar del proceso">
                                        <i class="fas fa-trash-alt mr-1"></i> Quitar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>        

    </div>
</div>


<style>
    .swal2-container {
        z-index: 10050 !important; /* debe ser mayor al z-[9999] */
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    const PROCESO_ID = {{ $proceso->getId() }};

    function abrirModalGestionEstudiantes() {
        document.getElementById('modal-gestion-estudiantes').classList.remove('hidden');
        document.getElementById('modal-gestion-estudiantes').classList.add('flex');
        document.getElementById('tabla-estudiantes').classList.add('hidden');
    }

    function cerrarModalGestionEstudiantes() {
        document.getElementById('modal-gestion-estudiantes').classList.add('hidden');
        document.getElementById('modal-gestion-estudiantes').classList.remove('flex');
    }

    function cargarListadoEstudiantes() 
    {
        const tabla = document.getElementById('tabla-estudiantes');
        const mensaje = document.getElementById('mensaje-sin-estudiantes');
        const loader = document.getElementById('loader-estudiantes');
        const filtro = document.getElementById("filtro-estudiantes");

        // Oculta todo
        tabla.classList.add('hidden');
        mensaje.classList.add('hidden');
        loader.classList.remove('hidden');

        // Obtener parámetros seleccionados
        const anio = document.getElementById('anio').value;
        const periodo = document.getElementById('periodo').value;
        const codigoPrograma = 74; 
        
        fetch(`/programa_academico/estudiantes-candidatos/${codigoPrograma}/${anio}/${periodo}`)
            .then(response => response.json())
            .then(data => {
                loader.classList.add('hidden');

                const estudiantes = data.data || [];

                if (estudiantes.length > 0) { 
                    
                    document.getElementById('estudiantes-encontrados').textContent = estudiantes.length;
                   
                    const tbody = document.querySelector("#tabla-estudiantes tbody");
                    tbody.innerHTML = ''; // Limpia filas anteriores

                    estudiantes.forEach(est => {

                        document.getElementById('creditos-pensum').textContent = data.creditosPensum || 'No disponible';

                        const fila = document.createElement('tr');
                        fila.className = 'bg-white hover:bg-blue-50 transition';

                        fila.innerHTML = `
                            <td class="px-4 py-2">${est.pensum || ''}</td>
                            <td class="px-4 py-2">${est.codigo || ''}</td>
                            <td class="px-4 py-2">${est.documento || ''}</td>
                            <td class="px-4 py-2">${est.nombre || ''}</td>
                            <td class="px-4 py-2">${est.ubicacionSemestre || ''}</td>
                            <td class="px-4 py-2"><span class="inline-block px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">${est.situacion || ''}</span></td>
                            <td class="px-2 py-2 text-center">${est.numeroCreditosPendientes ?? '-'}</td>
                            <td class="px-4 py-2 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer checkbox-estudiante" data-codigo="${est.codigo}" checked>
                                    <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-600 transition-colors relative">
                                        <div class="absolute top-[2px] left-[2px] w-5 h-5 bg-white rounded-full transition-transform peer-checked:translate-x-full"></div>
                                    </div>
                                </label>
                            </td>
                        `;

                        tbody.appendChild(fila);
                    });

                    tabla.classList.remove('hidden');
                } else {
                    mensaje.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error al cargar estudiantes:', error);
                loader.classList.add('hidden');
                mensaje.classList.remove('hidden');
            });

    }

    function filtrarEstudiantes() {
        const filtro = document.getElementById("filtro-estudiantes").value.toLowerCase();
        const filas = document.querySelectorAll("#tabla-estudiantes tbody tr");

        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? "" : "none";
        });
    }


    function guardarSeleccionados() {
        const seleccionados = Array.from(document.querySelectorAll('.checkbox-estudiante:checked'))
            .map(cb => cb.dataset.codigo);

        if (seleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin selección',
                text: 'Debes seleccionar al menos un estudiante.'
            });
            return;
        }

        const anio = document.getElementById('anio').value;
        const periodo = document.getElementById('periodo').value;

        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera un momento.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });        

        fetch('/programa_academico/asociar-estudiantes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                estudiantes: seleccionados,
                proc_id: PROCESO_ID,
                anio: anio,
                periodo: periodo                
            })
        })
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al guardar');
            }

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message || 'Estudiantes asociados correctamente'
            }).then(() => {
                cerrarModalGestionEstudiantes(); 
                refrescarBotonEstudiantesDesdeTemplate();
            });

        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'No se pudo guardar los estudiantes.'
            });
        });
    }


    function abrirModalEstudiantesVinculados() {
        document.getElementById('modal-estudiantes-vinculados').classList.remove('hidden');
        document.getElementById('modal-estudiantes-vinculados').classList.add('flex');
    }

    function cerrarModalEstudiantesVinculados() {
        document.getElementById('modal-estudiantes-vinculados').classList.add('hidden');
        document.getElementById('modal-estudiantes-vinculados').classList.remove('flex');
    }

    function refrescarBotonEstudiantesDesdeTemplate() {
        const template = document.getElementById('template-boton-estudiantes');
        const contenedor = document.getElementById('seccion-estudiantes-vinculados');

        if (template && contenedor) {
            contenedor.innerHTML = template.innerHTML;
        }
    }

    function verDetalleEstudiante(codigo) {
        Swal.fire({
            icon: 'info',
            title: 'Detalle del Estudiante',
            text: `Próximamente se mostrará el detalle del estudiante con código: ${codigo}`,
        });
    }

    function quitarEstudianteDelProceso(ppesId) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Este estudiante será desvinculado del proceso.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/programa-academico/estudiantes/${ppesId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message || 'Estudiante retirado correctamente'
                    }).then(() => {
                        // Recargar o refrescar la lista según tu lógica
                        cerrarModalEstudiantesVinculados();
                        location.reload(); // o refrescar sección si tienes render dinámico
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo quitar el estudiante. Intenta nuevamente.'
                    });
                });
            }
        });
    }   
    
    
    document.getElementById('buscador-estudiantes').addEventListener('input', function () {
        const filtro = this.value.toLowerCase().trim();
        const filas = document.querySelectorAll('#modal-estudiantes-vinculados tbody tr');

        filas.forEach(fila => {
            const textoFila = fila.innerText.toLowerCase();
            fila.style.display = textoFila.includes(filtro) ? '' : 'none';
        });
    });

</script>
