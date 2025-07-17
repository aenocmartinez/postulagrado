@extends('layouts.programa_academico_sin_notificacion')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 max-w-6xl mx-auto">

    <!-- 📌 Información del Programa -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-blue-900">{{ auth()->user()->programaAcademico()->getNombre() }}</h2>
        <p class="text-sm text-gray-600">Unidad Regional: <strong>{{ auth()->user()->programaAcademico()->getUnidadRegional()->getNombre() }}</strong></p>
        <p class="text-sm text-gray-600">Código SNIES: <strong>{{ auth()->user()->programaAcademico()->getSnies() }}</strong></p>
    </div>

</div>


@endsection


@section('scripts')
<script>
    let notificaciones = [];
    let indiceActual = 0;

    document.addEventListener("DOMContentLoaded", () => {
        const lista = [...document.querySelectorAll("#lista-no-leidas li, #lista-leidas li")];
        notificaciones = lista;
    });

    function mostrarContenido(li) {
        const titulo = li.getAttribute("data-titulo");
        const mensaje = li.getAttribute("data-mensaje");
        const contenedor = document.getElementById("modal-contenedor");

        // Ancho dinámico según longitud del mensaje
        const longitud = mensaje.length;
        contenedor.classList.remove("max-w-md", "max-w-xl", "max-w-3xl");
        if (longitud < 300) contenedor.classList.add("max-w-md");
        else if (longitud < 800) contenedor.classList.add("max-w-xl");
        else contenedor.classList.add("max-w-3xl");

        // Actualiza contenido
        document.getElementById("modal-titulo").textContent = titulo;
        document.getElementById("modal-mensaje").innerHTML = mensaje.replace(/\n/g, "<br>");

        // Mostrar modal
        document.getElementById("modal-notificacion").classList.remove("hidden");
        document.getElementById("modal-notificacion").classList.add("flex");

        // Índice actual
        indiceActual = notificaciones.findIndex(item => item === li);
    }

    function cerrarModal() {
        document.getElementById("modal-notificacion").classList.add("hidden");
        document.getElementById("modal-notificacion").classList.remove("flex");
    }

    function navegarNotificacion(direccion) {
        const nuevoIndice = indiceActual + direccion;
        if (nuevoIndice >= 0 && nuevoIndice < notificaciones.length) {
            mostrarContenido(notificaciones[nuevoIndice]);
        }
    }

    function marcarComoLeida(event, boton) {
        event.stopPropagation();

        const item = boton.closest("li");
        item.querySelector("button").remove(); // quita el botón
        const checkIcon = document.createElement("i");
        checkIcon.className = "fas fa-check-double text-sm text-gray-400";
        item.appendChild(checkIcon);

        document.getElementById("lista-leidas").appendChild(item);

        if (document.querySelectorAll("#lista-no-leidas li").length === 0) {
            document.getElementById("sin-recientes").classList.remove("hidden");
        }
    }
</script>
@endsection
