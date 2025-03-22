<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de PostulaGrado')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="bg-gray-100 font-sans h-screen flex flex-col">
    @php
        $httpCodes = [
            200 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => '✅'],
            201 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'icon' => '✅'],
            404 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'icon' => '⚠️'],
            409 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'icon' => '❌'],
        ];

        $alertCode = collect($httpCodes)->keys()->first(fn($code) => session()->has($code));
    @endphp

    @if ($alertCode)
        <div id="alert-message"
            class="w-full flex items-center justify-center px-4 py-3 border-b shadow transition-opacity duration-500 opacity-100
                    {{ $httpCodes[$alertCode]['bg'] }} {{ $httpCodes[$alertCode]['text'] }} {{ $httpCodes[$alertCode]['border'] }}">
            <div class="flex items-center gap-2 max-w-4xl w-full justify-center relative">
                <p class="text-sm font-medium text-center w-full">{{ session($alertCode) }}</p>

            </div>
        </div>
    @endif


    <div class="flex flex-1 h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-900 text-white p-6 flex flex-col justify-between shadow-lg sidebar">
            <div>
                <h1 class="text-2xl font-bold text-center mb-6">PostulaGrado</h1>
                <nav>
                    <ul class="space-y-2">
                        <!-- <li><a href="#" onclick="closeSidebarAfterClick()">Inicio</a></li> -->
                        <!-- <li>
                            <a href="#" onclick="toggleSubmenu(event, 'procesosSubmenu')" class="font-semibold">
                                Procesos de Grado
                                <svg class="menu-arrow w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <ul id="procesosSubmenu" class="submenu">
                                <li><a href="{{ route('procesos.index') }}" onclick="closeSidebarAfterClick()">Ver Procesos</a></li>
                                <li><a href="{{ route('procesos.create') }}" onclick="closeSidebarAfterClick()">Nuevo Proceso</a></li>
                            </ul>
                        </li> -->
                        <li>
                            <a href="{{ route('procesos.index') }}" 
                                class="{{ request()->routeIs('procesos.*') ? 'active' : '' }}"
                                onclick="closeSidebarAfterClick()">
                                Procesos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('seguimientos.index') }}" 
                                class="{{ request()->routeIs('seguimientos.*') ? 'active' : '' }}"
                                onclick="closeSidebarAfterClick()">
                                Seguimientos
                            </a>                            
                        </li>
                        <li>
                            <a href="{{ route('contactos.index') }}" 
                                class="{{ request()->routeIs('contactos.*') ? 'active' : '' }}"
                                onclick="closeSidebarAfterClick()">
                                Directorio de contactos
                            </a>                            
                        </li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Notificaciones</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Reportes</a></li>
                        <!-- <li><a href="#" onclick="closeSidebarAfterClick()">Tablero de Control</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Programador de Notificaciones</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Gestión de Usuarios</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Mensajes</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Notificaciones</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Perfil</a></li> -->
                    </ul>
                </nav>
            </div>

            <div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full text-center">
                    @csrf
                    <button type="submit"
                        onclick="closeSidebarAfterClick()"
                        class="block w-full px-4 py-3 text-center bg-red-600 rounded-lg hover:bg-red-700 transition">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 flex flex-col px-6 py-4">
            <!-- Header -->
            <header class="flex justify-between items-center bg-white shadow-md p-4 rounded-lg mb-4 border-b border-gray-300">
                <div class="flex items-center">
                    <span class="toggle-sidebar" onclick="toggleSidebar()">☰</span>
                    <h2 class="text-lg font-semibold text-blue-900">@yield('header', 'Dashboard')</h2>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                    <div class="relative">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Usuario"
                                 class="w-10 h-10 rounded-full border-2 border-blue-900 cursor-pointer">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Sección de Contenido y Notificaciones -->
            <div class="grid grid-cols-4 gap-4 mb-4 flex-1">
                <!-- Contenido (75%) -->
                <div class="col-span-3 bg-white shadow-md rounded-lg p-6 border border-gray-200">
                    @yield('content') 
                </div>
                
                <!-- Notificaciones más compactas con altura fija -->
                 <div id="seccion-notificaciones">

                     <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200 text-sm h-[300px] overflow-y-auto">
                         <h3 class="text-blue-900 text-base font-semibold">🔔 Últimas Notificaciones</h3>
                         <ul class="text-gray-700 space-y-2 mt-2 notification-list text-xs">
                             <li>📌 Nueva convocatoria abierta</li>
                             <li>⚠️ Actualiza tus datos antes del 10 de marzo</li>
                             <li>✅ Revisión de postulaciones completada</li>
                             <li>🔄 Mantenimiento programado el 15 de marzo</li>
                             <li>📅 Próximo cierre de postulaciones el 20 de marzo</li>
                             <li>🚀 Nueva actualización disponible</li>
                             <li>🔔 Recordatorio: Verificar documentos</li>
                         </ul>
                     </div>
                 </div>
            </div>

            <!-- Footer -->
            <footer class="text-center text-gray-600 p-3 mt-auto bg-white shadow-md 
                           rounded-lg border-t border-gray-300 text-xs">
                <p>© {{ date('Y') }} Universidad Colegio Mayor de Cundinamarca - Todos los derechos reservados</p>
            </footer>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
        }

        function toggleSubmenu(event, submenuId) {
            event.preventDefault();
            document.getElementById(submenuId).classList.toggle('active');
        }

        function closeSidebarAfterClick() {
            // document.getElementById('sidebar').classList.add('hidden');
        }
    </script>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('alert-message');
            if (alert) {
                alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => alert.remove(), 500);
            }
        }, 4000);
    </script>


    <!-- Sección para scripts adicionales -->
    @yield('scripts')

</body>
</html>
