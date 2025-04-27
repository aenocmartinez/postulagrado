@extends('layouts.app')

@section('title', 'Seguimiento a Procesos de Grado')

@section('header', 'Seguimiento a Procesos de Grado')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Seleccione el Proceso</h2>

    <div class="mb-6">
        <label for="proceso" class="block text-sm font-medium text-gray-700">Proceso Activo</label>
        <select id="proceso-select" class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none">
            <option value="">Seleccione un proceso</option>
            @foreach($procesos as $proceso)
                @if($proceso->getEstado() != "Abierto")
                    @continue
                @endif
                <option value="{{ route('seguimientos.show', ['id' => $proceso->getId()]) }}">
                    {{ $proceso->getNombre() . " - " . $proceso->getNivelEducativo()->getNombre() }}
                </option>
            @endforeach
        </select>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('proceso-select').addEventListener('change', function () {
        let url = this.value;
        if (url) {
            window.location.href = url;
        }
    });
</script>
@endsection
