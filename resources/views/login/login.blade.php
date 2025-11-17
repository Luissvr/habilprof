<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabilProf UCSC - Login</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Poppins (moderna y profesional) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Efecto de difuminado blanco sobre la imagen */
        .backdrop-overlay {
            /* ¡CAMBIO! Se reduce el difuminado a un 30% como pediste */
            /* (Tailwind no tiene 'bg-white/30', así que usamos CSS) */
            backdrop-filter: blur(8px);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.2));
        }
        
        /* Sombra suave para la tarjeta */
        .card-shadow {
            box-shadow: 0 20px 60px rgba(220, 38, 38, 0.15);
        }
        
        /* Animación suave al cargar */
        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Efecto hover en el botón */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }
    </style>
</head>

<body 
    class="bg-cover bg-center min-h-screen relative"
    style="background-image: url({{ asset('elogin/UCSC-CENTRAL.jpg') }});"
>
    <!-- Capa de difuminado blanco -->
    <div class="min-h-screen flex items-center justify-center p-2 backdrop-overlay">

        <!-- Tarjeta de Login -->
        <div class="login-card bg-gradient-to-br from-red-50 to-white p-10 rounded-2xl card-shadow w-full max-w-md border-2 border-red-100">

            <div class="flex justify-center mb-8">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('elogin/logo.png') }}" alt="Logo UCSC" class="h-22 w-22 object-contain">
                </div>
            </div>

            <!-- Título con acento rojo -->
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-2">
                Inicio de sesión
            </h1>
            <p class="text-center text-red-600 font-medium mb-8">Administración HabilProf</p>

            <form method="post" action="{{ route('login.validar') }}" class="space-y-6">
                @csrf

                <!-- Bloque de Error (Credenciales) -->
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-5 py-4 rounded-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Bloque de Errores (Validación) -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-5 py-4 rounded-lg" role="alert">
                        <strong class="font-bold block mb-2">¡Error!</strong>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Campo RUT (Usuario) - MÁS GRANDE -->
                <div>
                    <label for="usuario" class="block text-sm font-semibold text-gray-700 mb-2">
                        RUT (sin dígito verificador)
                    </label>
                    <div class="mt-1">
                        <input id="usuario" 
                               name="usuario" 
                               type="text" 
                               required
                               placeholder="Ej: 21069322"
                               minlength="7"
                               maxlength="8"
                               pattern="\d{7,8}"
                               class="block w-full px-5 py-4 text-lg rounded-xl border-2 border-red-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-200 transition-all duration-200"
                               value="{{ old('usuario') }}">
                    </div>
                </div>

                <!-- Campo Contraseña - MÁS GRANDE -->
                <div>
                    <label for="contrasenha" class="block text-sm font-semibold text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <div class="mt-1">
                        <input id="contrasenha" 
                               name="contrasenha" 
                               type="password" 
                               required
                               placeholder="••••••••"
                               minlength="6"
                               maxlength="8"
                               class="block w-full px-5 py-4 text-lg rounded-xl border-2 border-red-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-200 transition-all duration-200">
                    </div>
                </div>
                
                <!-- Botón de Ingresar - Más grande y con efecto hover -->
                <div class="pt-2">
                    <button type="submit" class="btn-hover w-full py-4 px-6 border border-transparent rounded-xl shadow-lg text-lg font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-4 focus:ring-red-300">
                        INGRESAR
                    </button>
                </div>
            </form>
            
            <!-- Pequeño detalle decorativo -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">UCSC © 2025</p>
            </div>
        </div>
        
    </div>
    
</body>
</html>