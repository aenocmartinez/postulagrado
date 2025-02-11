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
        .submenu {
            display: none;
            padding-left: 1rem;
        }
        .submenu.active {
            display: block;
        }
        .submenu li a {
            font-size: 0.8rem;
            padding-left: 1.5rem;
            color: #d1d5db;
        }
        .submenu li a:hover {
            color: white;
        }
        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.5rem;
            color: #1e3a8a;
            margin-right: 1rem;
        }
        .menu-arrow {
            transition: transform 0.3s ease-in-out;
        }
        .submenu.active ~ .menu-arrow {
            transform: rotate(90deg);
        }
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
                                <svg class="menu-arrow w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <ul id="procesosSubmenu" class="submenu">
                                <li><a href="#" onclick="closeSidebarAfterClick()">✔ Ver Procesos</a></li>
                                <li><a href="#" onclick="closeSidebarAfterClick()">✔ Nuevo Proceso</a></li>
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
            <header class="flex justify-between items-center bg-white shadow-md p-4 rounded-lg mb-4 border-b border-gray-300">
                <div class="flex items-center">
                    <span class="toggle-sidebar" onclick="toggleSidebar()">☰</span>
                    <h2 class="text-lg font-semibold text-blue-900">@yield('header', 'Dashboard')</h2>
                </div>
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
    </script>
</body>
</html>
