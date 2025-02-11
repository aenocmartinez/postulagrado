@extends('layouts.app')

@section('title', 'Listado de Procesos de Grado')

@section('header', 'Listado de Procesos de Grado')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Procesos de Grado</h2>
            <a href="{{ route('procesos.create') }}" 
               class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                + Nuevo proceso
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                <thead>
                    <tr class="bg-[#F2F2F5] text-gray-700 text-left">
                        <th class="px-4 py-2 font-medium">Proceso</th>
                        <th class="px-4 py-2 font-medium">Estado</th>
                        <th class="px-4 py-2 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($procesos as $proceso)
                        <tr class="border-b border-gray-300 bg-white hover:bg-gray-100 transition">
                            <td class="px-4 py-2 text-gray-900">{{ $proceso['nombre'] }}</td>
                            <td class="px-4 py-2 font-semibold {{ $proceso['estado'] == 'abierto' ? 'text-orange-500' : 'text-gray-900' }}">
                                {{ ucfirst($proceso['estado']) }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-4 text-gray-600">
                                    <!-- Botón Editar -->
                                    <a href="{{ route('procesos.edit', $proceso['id']) }}" 
                                       class="hover:text-blue-600 transition">
                                        Editar
                                    </a>

                                    <!-- Botón Calendario de Actividades -->
                                    <a href="{{ route('procesos.edit', $proceso['id']) }}" 
                                       class="hover:text-orange-400 transition">
                                        Calendario
                                    </a>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('procesos.destroy', $proceso['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-red-600 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
