<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - PostulaGrado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-900 to-blue-700">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logo_universidad.png') }}" alt="Logo Universidad" class="w-auto h-40 mx-auto">
        </div>
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Bienvenido a PostulaGrado</h2>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <input placeholder="Correo Electrónico" type="email" name="email" id="email" class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <input placeholder="Contraseña" type="password" name="password" id="password" class="w-full px-4 py-2 mt-1 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            

            <div class="mb-4">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="w-full px-4 py-2 text-white bg-blue-800 rounded-lg hover:bg-blue-900 transition">Iniciar Sesión</button>
            
            <p class="mt-4 text-sm text-center text-gray-600">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
            </p>
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
