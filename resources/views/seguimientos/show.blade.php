@extends('layouts.app')

@section('title', 'Tablero de Seguimiento')

@section('header', 'Seguimiento del Proceso')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-6xl mx-auto">
    
    <!-- 📌 Información del Proceso -->
    <div class="mb-6 flex justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Proceso: {{ $proceso->getNombre() }}</h2>
            <p class="text-sm text-gray-600">Nivel Educativo: <strong>{{ $proceso->getNivelEducativo() }}</strong></p>
            <p class="text-sm text-gray-600">Estado: 
                <span class="text-green-600">{{ $proceso->getEstado() }}</span>
            </p>
        </div>
    </div>

    <!-- 🔴🟡🟢 Segmentación de Actividades con Listado -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Actividades</h3>
    <div class="grid grid-cols-2 gap-6">
        @php
            $actividades = [
                'Finalizadas' => ['color' => 'gray-200', 'text' => 'text-gray-800', 'items' => [
                    ['Elaboración de Acta', '2024-02-10 - 2024-02-15'],
                    ['Revisión de Documentos', '2024-01-20 - 2024-02-05'],                    
                ]],
                'En Curso' => ['color' => 'green-200', 'text' => 'text-green-800', 'items' => [
                    ['Publicación de Actas', '2024-03-10 - 2024-03-15'],
                    ['Verificación de Datos', '2024-03-12 - 2024-03-18'],
                    ['Preparación de Diplomas', '2024-03-15 - 2024-03-20'],
                    ['Generación de Resoluciones', '2024-03-17 - 2024-03-22']
                ]],
                'Programadas' => ['color' => 'yellow-200', 'text' => 'text-yellow-800', 'items' => [
                    ['Entrega de Diplomas Oficiales', '2024-03-20 - 2024-03-25'],
                    ['Revisión de Notas Finales', '2024-03-22 - 2024-03-28'],
                    ['Generación de Actas de Grado', '2024-03-25 - 2024-03-30'],
                    ['Validación de Expedientes', '2024-03-28 - 2024-04-02']
                ]],
                'Próximas a Iniciar' => ['color' => 'orange-200', 'text' => 'text-orange-800', 'items' => [
                    ['Ceremonia de Grado', '2024-04-05 - 2024-04-07']
                ]]
            ];
        @endphp

        @foreach ($actividades as $titulo => $data)
        <div class="bg-{{ $data['color'] }} p-4 rounded-lg">
            <h4 class="text-sm font-semibold {{ $data['text'] }} mb-2 cursor-pointer" onclick="toggleLista('{{ strtolower(str_replace(' ', '_', $titulo)) }}')">
                🔹 {{ $titulo }} <span class="text-xs text-gray-500">(Ver más)</span>
            </h4>
            <div id="{{ strtolower(str_replace(' ', '_', $titulo)) }}" class="hidden">
                <ul class="list-disc pl-4">
                    @foreach ($data['items'] as $actividad)
                        <li class="text-xs {{ $data['text'] }}">{{ $actividad[0] }} ({{ $actividad[1] }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>    

    <!-- 📚 Avance de Programas Académicos (Listado en Tabla) -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Avance por Programa Académico</h3>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
            <thead>
                <tr class="bg-gray-200 text-gray-700 text-left">
                    <th class="px-4 py-2 font-medium">Programa Académico</th>
                    <th class="px-4 py-2 font-medium text-center">Avance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $programas = [
                        ['Ingeniería de Sistemas', 80],
                        ['Derecho', 65],
                        ['Administración de Empresas', 45],
                        ['Psicología', 90],
                        ['Contaduría Pública', 55],
                        ['Bacteriología', 35],
                        ['Medicina', 40],
                        ['Ingeniería electrónica', 60],
                        ['Economía', 25]
                    ];
                @endphp

                @foreach ($programas as [$nombre, $avance])
                <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                    <td class="px-4 py-2 text-gray-900">{{ $nombre }}</td>
                    <td class="px-4 py-2 text-center">
                        <div class="w-full bg-gray-300 rounded h-2">
                            <div class="h-2 bg-blue-500 rounded" style="width: {{ $avance }}%"></div>
                        </div>
                        <span class="text-xs text-gray-600">{{ $avance }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 📊 Paz y Salvo - Financiera y Biblioteca -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Estado de Paz y Salvo</h3>
    <div class="grid grid-cols-2 gap-6">
        @php
            $pazYSalvo = [
                'Financiera' => ['total' => 100, 'pendientes' => 10],
                'Admisiones' => ['total' => 100, 'pendientes' => 5],
                'Biblioteca' => ['total' => 100, 'pendientes' => 23],
                'Recursos educativos' => ['total' => 100, 'pendientes' => 15],
                'Centro de idiomas' => ['total' => 100, 'pendientes' => 30],
            ];
        @endphp

        @foreach ($pazYSalvo as $area => $datos)
        <div class="bg-gray-200 p-4 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-800 mb-2">🏛️ {{ $area }}</h4>
            <p class="text-xs text-gray-600">Total de Estudiantes: <strong>{{ $datos['total'] }}</strong></p>
            <p class="text-xs text-gray-600">Pendientes: <strong class="text-red-600">{{ $datos['pendientes'] }}</strong></p>
            <div class="w-full bg-gray-300 rounded mt-2">
                <div class="h-2 bg-blue-500 rounded" style="width: {{ (1 - $datos['pendientes'] / $datos['total']) * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>    

    <!-- 📩 Notificaciones -->
    <h3 class="text-md font-semibold text-gray-700 mt-6 mb-3">Notificaciones Enviadas</h3>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
            <thead>
                <tr class="bg-gray-200 text-gray-700 text-left">
                    <th class="px-4 py-2 font-medium">Notificación</th>
                    <th class="px-4 py-2 font-medium">Destinatario</th>
                    <th class="px-4 py-2 font-medium">Fecha de Envío</th>
                    <th class="px-4 py-2 font-medium">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach([['Validación de documentos', 'Ana Gómez', '2024-02-10', false], ['Firma de resolución', 'Carlos Rodríguez', '2024-02-12', true]] as [$mensaje, $usuario, $fecha, $leido])
                <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                    <td class="px-4 py-2 text-gray-900">{{ $mensaje }}</td>
                    <td class="px-4 py-2 text-gray-900">{{ $usuario }}</td>
                    <td class="px-4 py-2 text-gray-900">{{ $fecha }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $leido ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                            {{ $leido ? 'Leído' : 'Pendiente' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
<script>
    function toggleLista(id) {
        let element = document.getElementById(id);
        element.classList.toggle('hidden');
    }
</script>
@endsection
