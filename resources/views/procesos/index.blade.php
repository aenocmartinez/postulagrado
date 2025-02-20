@extends('layouts.app')

@section('title', 'Listado de Procesos de Grado')

@section('header', 'Listado de Procesos de Grado')

@section('content')


    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
        
        <!-- Buscador y botón de nuevo proceso -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Procesos de Grado</h2>
            
            <div class="flex space-x-2">
                <!-- Formulario de búsqueda -->
                <form method="GET" action="{{ route('procesos.index') }}" class="flex">
                    <input type="text" name="search" 
                        placeholder="Buscar proceso..." 
                        value="{{ request('search') }}"
                        class="border border-gray-300 px-3 py-2 rounded-l-md text-sm w-64 focus:ring focus:ring-gray-400 outline-none">

                    <button type="submit" class="bg-gray-700 px-4 py-2 rounded-r-md hover:bg-gray-800 transition flex items-center justify-center" aria-label="Buscar">
                        <i class="fas fa-magnifying-glass text-gray-300 text-sm opacity-75"></i>
                    </button>
                </form>

                <!-- Botón Nuevo Proceso -->
                <a href="{{ route('procesos.create') }}" 
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                    + Nuevo proceso
                </a>
            </div>

        </div>

        <!-- Tabla de procesos -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium">Proceso</th>
                        <th class="px-4 py-2 font-medium">Nivel educativo</th>
                        <th class="px-4 py-2 font-medium">Estado</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procesos as $proceso)
                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-900">{{ $proceso->getNombre() }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $proceso->getNivelEducativo() }}</td>
                            <td class="px-4 py-2 font-semibold {{ $proceso->getEstado() == 'Abierto' ? 'text-orange-500' : 'text-gray-900' }}">
                                {{ ucfirst($proceso->getEstado()) }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <!-- Botón Editar -->
                                    <a href="{{ route('procesos.edit', $proceso->getId()) }}" 
                                       class="hover:text-blue-600 transition">
                                        Editar
                                    </a>

                                    <!-- Botón Calendario de Actividades -->
                                    <a href="{{ route('procesos.edit', $proceso->getId()) }}" 
                                       class="hover:text-orange-400 transition">
                                        Calendario
                                    </a>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('procesos.destroy', $proceso->getId()) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-red-600 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">
                                No se encontraron procesos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginador -->
        <div class="mt-4">
            {{ $procesos->appends(['search' => request('search')])->links() }}
        </div>
    </div>
@endsection
