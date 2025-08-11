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
                                <th class="px-4 py-3">Categoria</th>
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

        <!-- Botón para agregar estudiante -->
        <div class="flex justify-between items-center mb-4 mt-2" id="boton-agregar-nuevo-estudiante">
            <div></div> <!-- espacio a la izquierda -->
            <button onclick="mostrarFormularioAgregarEstudiante()"
                    class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                <i class="fas fa-user-plus mr-1"></i> Agregar estudiante
            </button>
        </div>      
        
        <!-- Formulario oculto para agregar un nuevo estudiante -->
        <div id="formulario-agregar-estudiante" class="hidden mb-4 border border-green-200 p-4 rounded bg-green-50">
            <div class="flex justify-center mb-3">
                <div class="flex items-center gap-4 w-full md:w-2/3 lg:w-1/2">
                    <input type="text" id="busqueda-estudiante"
                        placeholder="Ingrese código o documento"
                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <button onclick="buscarEstudiante()"
                            class="bg-blue-600 text-white px-4 py-2 text-sm rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        Buscar
                    </button>
                </div>
            </div>

            <div id="resultado-busqueda-estudiante" class="text-sm text-gray-800"></div>
        </div>      

        <!-- Encabezado contextual -->
        <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded-md mb-4 text-sm text-gray-700 shadow-sm" id="encabezado-contextual">
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
                    <p>
                        <strong>Total vinculados:</strong>
                        <span id="total-vinculados">
                            {{ count(auth()->user()->programaAcademico()->listarEstudiantesCandidatos($proceso->getId())) }}
                        </span>
                    </p>
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
            <table class="min-w-full text-sm text-left border-collapse" id="tabla-estudiantes-vinculados-proceso">
                <thead class="bg-green-100 text-green-900 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">Pensum</th>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Documento</th>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Categoria</th>
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
                            <td class="px-4 py-2">{{ $est['detalle']->categoria ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                    {{ $est['detalle']->situacion ?? '-' }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center">{{ $est['detalle']->cred_pendientes ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Botón Ver -->
                                    <button 
                                        data-codigo="{{ $est['estu_codigo'] }}"
                                        onclick="verDetalleEstudiante(this.dataset.codigo)"
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        title="Ver detalles del estudiante">
                                        <i class="fas fa-search mr-1"></i> Ver
                                    </button>
                                    <!-- Botón Quitar -->
                                    <button 
                                        data-ppes-id="{{ $est['ppes_id'] }}"
                                        onclick="quitarEstudianteDelProceso(this.dataset.ppesId)"
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


            <!-- Vista de detalle del estudiante (inicialmente oculta) -->
            <div id="detalle-estudiante" class="hidden text-gray-800 space-y-10 px-4 py-6">

                <!-- Botón volver -->
                <div class="flex justify-end">
                    <button onclick="volverAListadoEstudiantes()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-full hover:bg-gray-200 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                    </button>
                </div>

                <!-- Métricas clave en círculos -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center shadow-inner text-xl font-bold text-green-800">
                            <span id="det-creditos">---</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">Créditos aprobados</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center shadow-inner text-xl font-bold text-blue-800">
                            <span id="det-formulario">---</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">Formulario actualizado</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center shadow-inner text-xl font-bold text-purple-800">
                            <span id="det-egresado">---</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">Egresado Unicolmayor</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-full bg-yellow-100 flex items-center justify-center shadow-inner text-xl font-bold text-yellow-800">
                            <span id="det-representante">---</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 font-medium">Representante Estudiantil</p>
                    </div>
                </div>

                <!-- Información personal y contacto -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Información personal -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h4 class="text-base font-semibold mb-4 flex items-center gap-2 text-gray-700">
                            <i class="fas fa-id-card text-indigo-500"></i> Información personal
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-500 w-5"></i>
                                <span><strong>Nombre:</strong> <span id="det-nombre"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-gray-500 w-5"></i>
                                <span><strong>Programa:</strong> <span id="det-programa"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-venus-mars text-gray-500 w-5"></i>
                                <span><strong>Género:</strong> <span id="det-genero"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-gray-500 w-5"></i>
                                <span><strong>Hijo de funcionario/docente:</strong> <span id="det-hijo-funcionario"></span></span>
                            </div>
                            <div class="flex items-center gap-2 hidden" id="campo-universidad-pregrado">
                                <i class="fas fa-university text-gray-500 w-5"></i>
                                <span><strong>Universidad Pregrado:</strong> <span id="det-universidad-pregrado"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h4 class="text-base font-semibold mb-4 flex items-center gap-2 text-gray-700">
                            <i class="fas fa-address-book text-blue-500"></i> Información de contacto
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-500 w-5"></i>
                                <span><strong>Correo:</strong> <span id="det-correo"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-500 w-5"></i>
                                <span><strong>Teléfono:</strong> <span id="det-telefono"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-id-badge text-gray-500 w-5"></i>
                                <span><strong>Documento:</strong> <span id="det-documento"></span></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-pdf text-gray-500 w-5"></i>
                                <span><strong>Ver documento:</strong>
                                    <a id="det-link-documento" href="#" target="_blank"
                                    class="text-blue-600 underline hover:text-blue-800">Abrir archivo</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de paz y salvo con barra de cumplimiento -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h4 class="text-base font-semibold mb-4 text-gray-700 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-500"></i> Estado de Paz y Salvo
                    </h4>

                    <!-- Barra de porcentaje -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Porcentaje de cumplimiento</span>
                            <span id="det-pazsalvo-porcentaje">0%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div id="barra-pazsalvo"
                                class="h-full bg-green-500 transition-all duration-500 ease-in-out"
                                style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Estados individuales -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div><p class="text-sm text-gray-500">Financiera</p><p class="font-medium" id="det-financiera">---</p></div>
                        <div><p class="text-sm text-gray-500">Admisiones</p><p class="font-medium" id="det-admisiones">---</p></div>
                        <div><p class="text-sm text-gray-500">Biblioteca</p><p class="font-medium" id="det-biblioteca">---</p></div>
                        <div><p class="text-sm text-gray-500">Recursos Educativos</p><p class="font-medium" id="det-recursos">---</p></div>
                        <div><p class="text-sm text-gray-500">Centro de Idiomas</p><p class="font-medium" id="det-idiomas">---</p></div>
                    </div>
                </div>

            </div>
            

        </div>        

    </div>
</div>


<style>
    .swal2-container {
        z-index: 10050 !important; /* debe ser mayor al z-[9999] */
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<span id="proceso" data-id="{{ $proceso->getId() }}"></span>
<script>
    const PROCESO_ID = document.getElementById('proceso').dataset.id;
</script>

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
                            <td class="px-4 py-2">${est.categoria || ''}</td>
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
        // Simulación de datos reales (reemplaza por un fetch si lo necesitas)
        const datos = {
            nombre: 'Juan Pérez Gómez',
            programa: 'Especialización en Gerencia Pública',
            creditos: 135,
            formularioActualizado: true,
            esEgresado: true,
            representante: false,
            genero: 'Masculino',
            hijoFuncionario: true,
            universidadPregrado: 'Universidad Nacional',
            correo: 'juan.perez@correo.edu.co',
            telefono: '3101234567',
            documento: '1020456789',
            documentoURL: '/documentos/1020456789.pdf',
            pazSalvo: {
                financiera: 'Paz y Salvo',
                admisiones: 'Pendiente',
                biblioteca: 'Pendiente',
                recursos: 'Pendiente',
                idiomas: 'Paz y Salvo'
            },
            esPostgrado: true
        };

        // Ocultar todo lo demás
        document.getElementById('tabla-estudiantes-vinculados-proceso')?.classList.add('hidden');
        document.getElementById('formulario-agregar-estudiante')?.classList.add('hidden');
        document.getElementById('buscador-estudiantes')?.parentElement.classList.add('hidden');
        document.getElementById('boton-agregar-nuevo-estudiante')?.classList.add('hidden');
        document.getElementById('encabezado-contextual')?.classList.add('hidden');

        // Mostrar sección de detalle
        document.getElementById('detalle-estudiante')?.classList.remove('hidden');

        // Círculos principales
        document.getElementById('det-creditos').innerText = datos.creditos;
        document.getElementById('det-formulario').innerText = datos.formularioActualizado ? 'Sí' : 'Pendiente';
        document.getElementById('det-egresado').innerText = datos.esEgresado ? 'Sí' : 'No';
        document.getElementById('det-representante').innerText = datos.representante ? 'Sí' : 'No';

        // Info personal
        document.getElementById('det-nombre').innerText = datos.nombre;
        document.getElementById('det-programa').innerText = datos.programa;
        document.getElementById('det-genero').innerText = datos.genero;
        document.getElementById('det-hijo-funcionario').innerText = datos.hijoFuncionario ? 'Sí' : 'No';

        if (datos.esPostgrado) {
            document.getElementById('campo-universidad-pregrado')?.classList.remove('hidden');
            document.getElementById('det-universidad-pregrado').innerText = datos.universidadPregrado;
        } else {
            document.getElementById('campo-universidad-pregrado')?.classList.add('hidden');
        }

        // Contacto
        document.getElementById('det-correo').innerText = datos.correo;
        document.getElementById('det-telefono').innerText = datos.telefono;
        document.getElementById('det-documento').innerText = datos.documento;
        document.getElementById('det-link-documento').href = datos.documentoURL;

        // Paz y salvo individuales
        document.getElementById('det-financiera').innerText = datos.pazSalvo.financiera;
        document.getElementById('det-admisiones').innerText = datos.pazSalvo.admisiones;
        document.getElementById('det-biblioteca').innerText = datos.pazSalvo.biblioteca;
        document.getElementById('det-recursos').innerText = datos.pazSalvo.recursos;
        document.getElementById('det-idiomas').innerText = datos.pazSalvo.idiomas;

        // Calcular porcentaje
        const estados = Object.values(datos.pazSalvo);
        calcularPorcentajePazYSalvo(estados);
    }

    function calcularPorcentajePazYSalvo(estados) {
        const total = estados.length;
        const cumplidos = estados.filter(e => e.toLowerCase() === 'paz y salvo').length;
        const porcentaje = Math.round((cumplidos / total) * 100);
        document.getElementById('det-pazsalvo-porcentaje').innerText = `${porcentaje}%`;
        document.getElementById('barra-pazsalvo').style.width = `${porcentaje}%`;
        document.getElementById('barra-pazsalvo').classList.toggle('bg-green-500', porcentaje === 100);
        document.getElementById('barra-pazsalvo').classList.toggle('bg-blue-400', porcentaje < 100 && porcentaje >= 60);
        document.getElementById('barra-pazsalvo').classList.toggle('bg-red-500', porcentaje < 60);
    }

    function volverAListadoEstudiantes() {
        // Ocultar el detalle
        document.getElementById('detalle-estudiante')?.classList.add('hidden');

        // Mostrar nuevamente la vista principal
        document.getElementById('tabla-estudiantes-vinculados-proceso')?.classList.remove('hidden');
        document.getElementById('formulario-agregar-estudiante')?.classList.remove('hidden');
        document.getElementById('buscador-estudiantes')?.parentElement.classList.remove('hidden');
        document.getElementById('boton-agregar-nuevo-estudiante')?.classList.remove('hidden');
        document.getElementById('encabezado-contextual')?.classList.remove('hidden');

        // Mantener el formulario cerrado
        document.getElementById('formulario-agregar-estudiante')?.classList.add('hidden');
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

<script>
    function mostrarFormularioAgregarEstudiante() {
        document.getElementById('formulario-agregar-estudiante').classList.remove('hidden');
    }

    async function buscarEstudiante() {
        const termino = document.getElementById('busqueda-estudiante').value.trim();
        if (!termino) return;

        const resultadoContenedor = document.getElementById('resultado-busqueda-estudiante');
        resultadoContenedor.innerHTML = 'Buscando...';

        try {
            const response = await fetch(`/proceso-estudiante/buscar?termino=${encodeURIComponent(termino)}`);
            const data = await response.json();

            if (!data || data.length === 0) {
                resultadoContenedor.innerHTML = '<p class="text-red-600">No se encontraron resultados.</p>';
                return;
            }

            // Mostrar resultados
            resultadoContenedor.innerHTML = data.map(est => `
                <div class="flex justify-between items-center border-b py-2">
                    <div>
                        <p><strong>${est.nombres}</strong> - ${est.estp_codigomatricula} - ${est.documento}</p>
                    </div>
                    <button onclick="agregarEstudianteAlProceso(${est.estp_codigomatricula})"
                            class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                        Agregar
                    </button>
                </div>
            `).join('');
        } catch (err) {
            resultadoContenedor.innerHTML = '<p class="text-red-600">Error al buscar estudiante.</p>';
        }
    }

    async function agregarEstudianteAlProceso(estudianteId) {
    const confirmacion = await Swal.fire({
        title: '¿Está seguro?',
        text: 'Este estudiante será vinculado al proceso de grado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, agregar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
    });
    if (!confirmacion.isConfirmed) return;

    // Loading
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espere un momento',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const res = await fetch(`/proceso-estudiante/agregar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ codigo: estudianteId, proceso_id: PROCESO_ID })
        });

        const data = await res.json();

        if (!res.ok || data.code !== 200) {
        throw new Error(data.message || 'No se pudo agregar el estudiante.');
        }

        // Éxito
        Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: data.message || 'Estudiante agregado correctamente.',
        timer: 1400,
        showConfirmButton: false
        });

        // 1) Ocultar y limpiar formulario de agregar
        document.getElementById('formulario-agregar-estudiante')?.classList.add('hidden');
        const input = document.getElementById('busqueda-estudiante');
        if (input) input.value = '';
        const resDiv = document.getElementById('resultado-busqueda-estudiante');
        if (resDiv) resDiv.innerHTML = '';

        // 2) Insertar/actualizar la fila sin recargar
        if (data.row_html) {
        const tbody = document.querySelector('#tabla-estudiantes-vinculados-proceso tbody');
        if (tbody) {
            // Parseamos el HTML para detectar el código y evitar duplicados
            const temp = document.createElement('tbody');
            temp.innerHTML = data.row_html.trim();
            const newRow = temp.querySelector('tr');
            const codigo = newRow?.getAttribute('data-codigo');

            if (codigo) {
            const existente = tbody.querySelector(`tr[data-codigo="${codigo}"]`);
            if (existente) {
                existente.replaceWith(newRow);
            } else {
                tbody.insertAdjacentElement('afterbegin', newRow);
            }
            } else {
            // Si no vino data-codigo, inserta sin reemplazo
            tbody.insertAdjacentHTML('afterbegin', data.row_html);
            }
        }
        }

        // 3) Actualizar contador “Total vinculados”
        const totalSpan = document.getElementById('total-vinculados');
        if (totalSpan) {
        const nuevoTotal = (parseInt(totalSpan.textContent || '0', 10) + 1);
        totalSpan.textContent = String(isNaN(nuevoTotal) ? 1 : nuevoTotal);
        } else {
        // Fallback al layout actual (ajústalo si cambias el encabezado)
        const encabezado = document.getElementById('encabezado-contextual');
        const strongs = encabezado ? encabezado.querySelectorAll('p strong') : [];
        const totalStrong = strongs[strongs.length - 1];
        if (totalStrong) {
            const actual = parseInt(totalStrong.textContent || '0', 10);
            totalStrong.textContent = String(isNaN(actual) ? 1 : actual + 1);
        }
        }

        // 4) Asegurar que se vea el listado (por si estabas en el detalle)
        document.getElementById('detalle-estudiante')?.classList.add('hidden');
        document.getElementById('tabla-estudiantes-vinculados-proceso')?.classList.remove('hidden');
        document.getElementById('buscador-estudiantes')?.parentElement.classList.remove('hidden');
        document.getElementById('boton-agregar-nuevo-estudiante')?.classList.remove('hidden');
        document.getElementById('encabezado-contextual')?.classList.remove('hidden');

    } catch (err) {
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message || 'Ocurrió un error al intentar agregar el estudiante.'
        });
    }
    }

</script>


<script>
async function quitarEstudianteDelProceso(ppesId) {
  // Confirmación
  const confirmacion = await Swal.fire({
    title: '¿Está seguro?',
    text: 'Este estudiante será desvinculado del proceso.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, quitar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
  });
  if (!confirmacion.isConfirmed) return;

  // Loading
  Swal.fire({
    title: 'Procesando...',
    text: 'Eliminando estudiante del proceso',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    // Petición DELETE
    const res = await fetch(`/programa-academico/estudiantes/${ppesId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      }
    });
    const data = await res.json();
    if (!res.ok || (data.code && data.code !== 200)) {
      throw new Error(data.message || 'No se pudo quitar el estudiante.');
    }

    // Éxito
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: data.message || 'Estudiante retirado correctamente',
      timer: 1400,
      showConfirmButton: false
    });

    // Quitar la fila del DOM por ppesId
    const btn = document.querySelector(`button[data-ppes-id="${ppesId}"]`);
    const row = btn ? btn.closest('tr') : null;
    if (row) row.remove();

    // Actualizar contador "Total vinculados"
    const totalSpan = document.getElementById('total-vinculados');
    if (totalSpan) {
      const nuevo = Math.max(0, (parseInt(totalSpan.textContent || '0', 10) - 1));
      totalSpan.textContent = String(nuevo);
    } else {
      // Fallback si aún no usas <span id="total-vinculados">
      const encabezado = document.getElementById('encabezado-contextual');
      const strongs = encabezado ? encabezado.querySelectorAll('p strong') : [];
      const totalStrong = strongs[strongs.length - 1];
      if (totalStrong) {
        const actual = parseInt(totalStrong.textContent || '0', 10);
        totalStrong.textContent = String(Math.max(0, actual - 1));
      }
    }

    // (Opcional) Asegurar que se vea el listado por si estabas en el detalle
    document.getElementById('detalle-estudiante')?.classList.add('hidden');
    document.getElementById('tabla-estudiantes-vinculados-proceso')?.classList.remove('hidden');
    document.getElementById('buscador-estudiantes')?.parentElement.classList.remove('hidden');
    document.getElementById('boton-agregar-nuevo-estudiante')?.classList.remove('hidden');
    document.getElementById('encabezado-contextual')?.classList.remove('hidden');

  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message || 'No se pudo quitar el estudiante. Intenta nuevamente.'
    });
  }
}
</script>
