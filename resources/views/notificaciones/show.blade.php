@extends('layouts.app')

@section('title', 'Detalle de Notificación')

@section('header', 'Detalle de Notificación')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-4xl mx-auto">

    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $notificacion->getAsunto() }}</h2>

        <p class="text-sm text-gray-600 mb-1">
            <strong>Canal de Envío:</strong> {{ ucfirst($notificacion->getCanal()) }}
        </p>

        <p class="text-sm text-gray-600 mb-1">
            <strong>Fecha de Envío:</strong> {{ \Carbon\Carbon::parse($notificacion->getFechaCreacion())->format('d/m/Y H:i') }}
        </p>

        <p class="text-sm text-gray-600 mb-1">
            <strong>Destinatarios:</strong> 
            {{ count(explode(',', $notificacion->getDestinatarios())) }} destinatario(s)
            @if($notificacion->getDestinatarios())
                <button onclick="mostrarModalDestinatarios()" class="text-blue-600 hover:underline ml-2 text-sm">
                    Ver listado
                </button>
            @endif
        </p>
    </div>

    <div class="bg-gray-50 p-4 rounded-lg border border-gray-300">
        <h3 class="text-md font-semibold text-gray-700 mb-2">Mensaje:</h3>
        <div class="text-gray-800 text-sm whitespace-pre-line">
            {!! $notificacion->getMensaje() !!}
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('notificaciones.index') }}" 
           class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
            Volver al Listado
        </a>
    </div>

</div>

<!-- Modal de Destinatarios -->
<div id="modal-destinatarios" class="hidden fixed inset-0 bg-gray bg-opacity-30 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md relative shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Destinatarios</h2>
        <ul class="text-gray-700 text-sm max-h-60 overflow-y-auto">
            @foreach(explode(',', $notificacion->getDestinatarios()) as $destinatario)
                <li class="mb-2">{{ trim($destinatario) }}</li>
            @endforeach
        </ul>
        <button onclick="cerrarModalDestinatarios()" 
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            ✖
        </button>
    </div>
</div>


@endsection

@section('scripts')
<script>
function mostrarModalDestinatarios() {
    document.getElementById('modal-destinatarios').classList.remove('hidden');
}

function cerrarModalDestinatarios() {
    document.getElementById('modal-destinatarios').classList.add('hidden');
}
</script>
@endsection
