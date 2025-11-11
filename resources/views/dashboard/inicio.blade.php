<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabilProf UCSC - Dashboard</title>
    
    <!-- 1. Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. Google Fonts - Poppins (La misma del Login) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .menu {
            display: none; /* Oculto por defecto */
            transition: all 0.3s ease-out;
        }
        .menu.mostrar {
            display: block; /* Visible cuando tu JS añade la clase */
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- ============================================== -->
    <!-- NAVEGACIÓN (HEADER)                          -->
    <!-- ============================================== -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Título (Izquierda) -->
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-gray-800">Menú HabilProf</h1>
                </div>
                
                <!-- Botón Cerrar Sesión (Derecha) -->
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type"submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-sm">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================== -->
    <!-- CONTENIDO PRINCIPAL (LAS TARJETAS)           -->
    <!-- ============================================== -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Grid de 3 columnas (se ajusta a 1 en móvil) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- 
                  TARJETA 1: Ingreso (R2)
                  Esta es una tarjeta-link directa.
                -->
                <a href="/ingreso" class="block bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-600 mb-4">
                        <!-- Icono de "Añadir" -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Ingreso de Habilitación</h2>
                    <p class="text-gray-600 text-sm">Registrar una nueva habilitación profesional (PrIng, PrInv o PrTut).</p>
                </a>

                <!-- 
                  TARJETA 2: Actualizar/Eliminar (R3)
                  Esta es una tarjeta-menú.
                -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600 mb-4">
                        <!-- Icono de "Editar" -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Actualizar o Eliminar</h2>
                    <p class="text-gray-600 text-sm mb-4">Editar o borrar registros de habilitaciones ya existentes.</p>
                    
                    <!-- Botón que activa tu JS -->
                    <button onclick="abrirMenu('menu')" class="w-full text-left font-medium text-blue-600 hover:text-blue-800">
                        Mostrar opciones...
                    </button>

                    <!-- Menú oculto (tu HTML original) -->
                    <div class="menu mt-4 space-y-2" id="menu">
                        <a href="###" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-700">
                           Eliminar habilitación
                        </a>
                        <a href="##" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-700">
                           Actualizar habilitación
                        </a>
                    </div>
                </div>

                <!-- 
                  TARJETA 3: Listados Varios
                -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600 mb-4">
                        <!-- Icono de "Listado" -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Listados Varios</h2>
                    <p class="text-gray-600 text-sm mb-4">Ver reportes semestrales o el listado histórico de habilitaciones.</p>

                    <!-- Botón que activa tu JS -->
                    <button onclick="abrirMenu('menu2')" class="w-full text-left font-medium text-green-600 hover:text-green-800">
                        Mostrar reportes...
                    </button>

                    <!-- Menú oculto (tu HTML original) -->
                    <div class="menu mt-4 space-y-2" id="menu2">
                         <a href="###" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-700">
                           Listado Semestral
                         </a>
                         <a href="##" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-700">
                           Listado Histórico
                         </a>
                    </div>
                </div>

            </div> <!-- Fin del Grid -->
        </div>
    </main>

    <script>
        function abrirMenu(idMenu) {
            const menus = document.querySelectorAll('.menu');
            const menuSeleccionado = document.getElementById(idMenu);

            // Cierra todos los menús excepto el seleccionado
            menus.forEach(m => {
                if (m !== menuSeleccionado) {
                    m.classList.remove('mostrar');
                }
            });

            // Abre o cierra el menú seleccionado
            menuSeleccionado.classList.toggle('mostrar');
        }
    </script>
</body>
</html>