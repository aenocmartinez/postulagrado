@extends('layouts.app')

@section('title', 'Crear Notificación')

@section('header', 'Crear Notificación')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-5xl mx-auto">

    <form action="{{ route('notificaciones.store') }}" method="POST">
        @csrf
        <input type="hidden" name="proceso_id" value="{{ $procesoNotificacion->procesoID }}">

        <!-- Asunto -->
        <div class="mb-6">
            <label for="asunto" class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
            <input type="text" name="asunto" id="asunto"
                class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none"
                placeholder="Asunto de la notificación" required>
        </div>

        <!-- Canal -->
        <div class="mb-6">
            <label for="canal" class="block text-sm font-medium text-gray-700 mb-2">Canal de Envío</label>
            <select name="canal" id="canal"
                class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none" required>
                <option value="Correo electrónico" selected>Correo Electrónico</option>
            </select>
        </div>

        <!-- Fecha de Envío -->
        <div class="mb-6">
            <label for="fecha_envio" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Envío</label>
            <input type="date" name="fecha_envio" id="fecha_envio"
                class="border border-gray-300 px-3 py-2 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none">
            <small class="text-gray-500 mt-1 block">Si no seleccionas una fecha, la notificación se enviará inmediatamente.</small>
        </div>


        <!-- Destinatarios -->
        <div class="mb-8">
            <label for="destinatarios" class="block text-sm font-medium text-gray-700 mb-2">Destinatarios</label>
            <select name="destinatarios[]" id="destinatarios" multiple required>
                <option value="__todos__">[Seleccionar Todos]</option>
                @foreach($procesoNotificacion->contactos as $contacto)
                    <option value="{{ $contacto->getEmail() }}">
                        {{ $contacto->getNombre() }} ({{ $contacto->getEmail() }})
                    </option>
                @endforeach
            </select>
            <small class="text-gray-500 mt-1 block">Busca y selecciona uno o varios destinatarios.</small>
        </div>

        <!-- Mensaje -->
        <div class="mb-8 mt-4">
            <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
            <textarea name="mensaje" id="mensaje"
                    class="border border-gray-300 rounded-md text-sm w-full focus:ring focus:ring-gray-400 outline-none"
                    rows="10"></textarea>
        </div>

        <!-- Botón Guardar -->
        <div class="flex justify-end mt-4">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 transition">
                Crear Notificación
            </button>
        </div>

    </form>

</div>

@endsection

@section('scripts')

<!-- Cargar Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<!-- Cargar CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let editorInstance;

    // Inicializar Tom Select
    const select = new TomSelect('#destinatarios', {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        onItemAdd: function(value) {
            if (value === '__todos__') {
                const control = this;
                control.clear(); // Borrar 'todos'

                const allValues = Array.from(document.querySelectorAll('#destinatarios option'))
                    .filter(opt => opt.value !== '__todos__')
                    .map(opt => opt.value);

                control.addItems(allValues);
            }
        },
        placeholder: "Selecciona destinatarios...",
        maxOptions: 1000,
        searchField: ['text'],
    });

    // Inicializar CKEditor
    ClassicEditor
        .create(document.querySelector('#mensaje'), {
            toolbar: {
                items: [
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'link', 'blockQuote', '|',
                    'undo', 'redo'
                ]
            }
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Capturar el submit del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        if (editorInstance) {
            const contenido = editorInstance.getData().trim();
            document.querySelector('#mensaje').value = contenido;

            if (contenido === '') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Mensaje vacío',
                    text: 'Debes escribir un mensaje antes de enviar.',
                });
            }
        }
    });
});
</script>


@endsection
