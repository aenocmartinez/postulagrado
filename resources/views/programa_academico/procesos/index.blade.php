@extends('layouts.programa_academico_sin_notificacion')

@section('title', 'Seguimiento a Procesos de Grado')

@section('header', 'Seguimiento a Procesos de Grado')

@section('content')

<div class="bg-white shadow-md rounded-lg p-8 border border-gray-200 w-full max-w-6xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Seleccione el Proceso</h2>

    <div class="mb-6">
        <label for="proceso" class="block text-sm font-medium text-gray-700 mb-1">Proceso Activo</label>
        <select id="proceso-select" class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none">
            <option value="">Seleccione un proceso</option>
            @foreach($procesos as $proceso)
                @if($proceso->estaCerrado())
                    @continue
                @endif
                <option value="{{ route('programa_academico.procesos.seguimiento', ['id' => $proceso->id]) }}">
                    {{ $proceso->nombre . " - " . $proceso->nivelEducativoNombre }}
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
