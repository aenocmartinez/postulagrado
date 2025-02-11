<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de PostulaGrado')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar {
            transition: width 0.3s ease-in-out;
            width: 16rem;
            overflow-y: auto;
            max-height: 100vh;
            position: relative;
            background: #1e3a8a;
            z-index: 1000;
        }
        .sidebar.hidden {
            width: 0;
            overflow: hidden;
        }
        .sidebar ul li {
            position: relative;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .menu-arrow {
            transition: transform 0.3s ease-in-out;
        }
        .submenu {
            display: none;           /* Hidden by default */
            padding-left: 1rem;     /* Indent sub-items */
        }
        .submenu.active {
            display: block;         /* Show sub-items when active */
        }
        /* Rotate arrow when submenu is active */
        .submenu.active ~ .menu-arrow {
            transform: rotate(90deg);
        }
        /* Submenu items style */
        .submenu li a {
            font-size: 0.8rem;
            padding-left: 1.5rem;
            color: #d1d5db;
            display: flex;
            align-items: center;
        }
        /* Bullet for submenu items */
        .submenu li a::before {
            content: '\2022';
            color: white;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        /* Hover effect on submenu items */
        .submenu li a:hover {
            color: white;
        }

        /* Toggle button in header */
        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.5rem;
            color: #1e3a8a;
            margin-right: 1rem;
        }

        /* Avatar placeholder */
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1e3a8a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            border: 2px solid #1e3a8a;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-900 text-white p-6 flex flex-col justify-between shadow-lg sidebar">
            <div>
                <h1 class="text-2xl font-bold text-center mb-6">PostulaGrado</h1>
                <nav>
                    <ul class="space-y-2">
                        <li><a href="#" onclick="closeSidebarAfterClick()">Inicio</a></li>
                        <li>
                            <a href="#" onclick="toggleSubmenu(event, 'procesosSubmenu')" class="font-semibold">
                                Procesos de Grado
                                <!-- Parent arrow -->
                                <svg class="menu-arrow w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <!-- Submenu -->
                            <ul id="procesosSubmenu" class="submenu">
                                <li><a href="#" onclick="closeSidebarAfterClick()">Ver Procesos</a></li>
                                <li><a href="#" onclick="closeSidebarAfterClick()">Nuevo Proceso</a></li>
                            </ul>
                        </li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Calendario de Actividades</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Tablero de Control</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Programador de Notificaciones</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Gestión de Usuarios</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Mensajes</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Notificaciones</a></li>
                        <li><a href="#" onclick="closeSidebarAfterClick()">Perfil</a></li>
                    </ul>
                </nav>
            </div>
            <div>
                <a href="#" class="block px-4 py-3 text-center bg-red-600 rounded-lg hover:bg-red-700 transition" onclick="closeSidebarAfterClick()">Cerrar Sesión</a>
            </div>
        </aside>
    
        <!-- Contenido Principal -->
        <main class="flex-1 flex flex-col px-6 py-4">
            <!-- Header -->
            <header class="flex justify-between items-center bg-white shadow-md p-4 rounded-lg mb-4 border-b border-gray-300">
                <div class="flex items-center">
                    <!-- Toggle sidebar button -->
                    <span class="toggle-sidebar" onclick="toggleSidebar()">☰</span>
                    <h2 class="text-lg font-semibold text-blue-900">@yield('header', 'Dashboard')</h2>
                </div>
                <!-- Name & Avatar -->
                <div class="flex items-center space-x-6">
                    <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                    <div class="relative">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Usuario" class="w-10 h-10 rounded-full border-2 border-blue-900 cursor-pointer">
                        @else
                            <div class="avatar-placeholder">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Content & Notifications -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <!-- Cards/Content -->
                <div class="col-span-2 bg-white shadow-md rounded-lg p-4 border border-gray-200">
                    @yield('content')
                </div>
                <!-- Notifications -->
                <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                    <h3 class="text-blue-900">🔔 Últimas Notificaciones</h3>
                    <ul class="text-gray-700 text-sm space-y-2 mt-2 notification-list">
                        <li>📌 Nueva convocatoria abierta</li>
                        <li>⚠️ Actualiza tus datos antes del 10 de marzo</li>
                        <li>✅ Revisión de postulaciones completada</li>
                    </ul>
                </div>
            </div>

            <!-- FOOTER -->
            <footer class="text-center text-gray-600 p-3 mt-4 bg-white shadow-md rounded-lg border-t border-gray-300 text-xs">
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
            // Optional: close the sidebar after a menu link is clicked
            document.getElementById('sidebar').classList.add('hidden');
        }
    </script>
</body>
</html>
