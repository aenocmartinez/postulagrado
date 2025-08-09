<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 text-sm h-auto">
    <h3 class="text-blue-900 text-lg font-bold text-center">
        {{ $programaProceso->getPrograma()->getNombre() }}
    </h3>
    <p class="text-gray-600 text-sm mt-1 text-center font-semibold">
        {{ $programaProceso->getPrograma()->getUnidadRegional()->getNombre() }}
    </p>
    <p class="text-gray-600 text-sm mt-1 text-center">
        Código SNIES: <span class="font-semibold text-gray-800">
        {{ $programaProceso->getPrograma()->getSnies() }}
        </span>
    </p>

    @php
        $estudiantesCanditados = $programaProceso->getCandidatosAGrado();
    @endphp

    <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
        <div class="bg-gray-100 p-4 rounded-lg">
            <h4 class="text-gray-700 font-semibold text-center">Candidatos a Grado</h4>
            <p class="text-blue-900 text-xl font-bold text-center">{{ count($estudiantesCanditados) }}</p>
        </div>

        <div class="bg-gray-100 p-4 rounded-lg">
            <h4 class="text-gray-700 font-semibold text-center">Estudiantes Aprobados</h4>
            <p class="text-green-700 text-xl font-bold text-center">0</p>
        </div>
    </div>

    <h4 class="text-gray-700 font-semibold mt-6 text-center">Estado de Aprobación por Área</h4>
    <div class="mt-2 text-gray-700">
        <div class="grid grid-cols-3 gap-1 font-semibold bg-gray-200 p-2 rounded-md">
            <span>Área</span>
            <span class="text-orange-500 text-center">PENDTES</span>
            <span class="text-red-500 text-center">RECHZDS</span>
        </div>

        <ul class="mt-2 space-y-2">
            <li class="grid grid-cols-3 gap-4 border-b pb-1">
                <span>Secretaría Académica</span>
                <span class="text-orange-500 text-center">0</span>
                <span class="text-red-500 text-center">0</span>
            </li>
            <li class="grid grid-cols-3 gap-4 border-b pb-1">
                <span>Tesorería</span>
                <span class="text-orange-500 text-center">0</span>
                <span class="text-red-500 text-center">0</span>
            </li>
            <li class="grid grid-cols-3 gap-4 border-b pb-1">
                <span>Biblioteca</span>
                <span class="text-orange-500 text-center">0</span>
                <span class="text-red-500 text-center">0</span>
            </li>
            <li class="grid grid-cols-3 gap-4 border-b pb-1">
                <span>Bienestar Universitario</span>
                <span class="text-orange-500 text-center">0</span>
                <span class="text-red-500 text-center">0</span>
            </li>
        </ul>
    </div>
</div>
