@if (auth()->user()->programaAcademico()->tieneCandidatosAsocidos($proceso->getId()))
    <p class="text-sm text-gray-600 mb-3">
        Haz clic en el botón para ver los estudiantes vinculados al proceso.
    </p>
    <a href="#"
       onclick="abrirModalEstudiantesVinculados()"
       class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
       aria-label="Ver estudiantes vinculados">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
        </svg>
        Ver estudiantes
    </a>
@else
    <p class="text-sm text-gray-600 mb-3">
        Actualmente no hay estudiantes registrados como candidatos a grado en este programa.
    </p>
    <a href="#"
       onclick="abrirModalGestionEstudiantes()"
       class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition"
       aria-label="Registrar estudiantes candidatos a grado">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
        </svg>
        Gestionar Estudiantes
    </a>
@endif
