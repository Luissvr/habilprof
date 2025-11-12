<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabilProf UCSC - Dashboard</title>
    
    <!-- 1. Tailwind CSS --><script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. Google Fonts - Poppins (La misma del Login) --><link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- 3. Iconos (para el menú) --><script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* --- ESTILOS PARA LOS NUEVOS BOTONES (Rectángulo Rojo) --- */
        .tab-boton { transition: all 0.2s ease-in-out; color: #FECACA; background-color: transparent; font-weight: 500; }
        .tab-boton:hover { color: #FFFFFF; background-color: #B91C1C; }
        .tab-boton.activo { color: #FFFFFF; background-color: #991B1B; font-weight: 600; box-shadow: inset 0 -4px 0 0 #DC2626; }
        
        /* --- ESTILOS PARA LOS PANELES DE CONTENIDO --- */
        .tab-panel { display: none; animation: fadeIn 0.5s ease-out; }
        .tab-panel.activo { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* --- (Estilos de 'ingreso.blade.php' movidos aquí) --- */
        .lista_alumno, .lista_profesores {
            display: none; border: 1px solid #e2e8f0; max-height: 150px;
            overflow-y: auto; position: absolute; background-color: white;
            width: 100%; z-index: 50; border-radius: 0 0 0.375rem 0.375rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .lista_alumno .item, .lista_profesores .item { padding: 8px 12px; cursor: pointer; }
        .lista_alumno .item:hover, .lista_profesores .item:hover { background-color: #f1f5f9; }
        .ruts { position: relative; }
        .proyecto, .practica { display: none; }
        .proyecto.mostrar, .practica.mostrar { display: block; }

        /* --- ESTILOS PARA EL SELECTOR "No-Noob" (Tipo Habilitación) --- */
        .tab-hab {
            background-color: transparent;
            color: #4B5563; /* text-gray-600 */
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
        }
        .tab-hab:hover {
            background-color: #F9FAFB; /* bg-gray-50 */
        }
        .tab-hab.activo-hab { /* El botón activo */
            background-color: #FFFFFF; /* bg-white */
            color: #DC2626; /* text-red-600 */
            font-weight: 600;
            border-color: #FCA5A5; /* border-red-300 */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- ============================================== --><!-- NAVEGACIÓN (HEADER) - ¡LOGO CORREGIDO!       --><!-- ============================================== --><nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10"> <!-- Ajuste ligero de padding --><div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <!-- ¡IMAGEN CORREGIDA! --><img src="{{ asset('elogin/logo.png') }}" alt="Logo UCSC" class="h-12"> <!-- Ajuste de altura a h-12 --><h1 class="text-xl font-bold text-gray-800">Panel HabilProf</h1>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 text-sm shadow-sm hover:shadow-md">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================== --><!-- MENÚ DE BOTONES (EL "RECTÁNGULO ROJO") - (Sin cambios) --><!-- ============================================== --><div class="bg-red-600 shadow-lg sticky top-16 z-30"> 
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-3">
                <button onclick="mostrarPanel('panel-ingreso', this)" class="tab-boton activo flex items-center justify-center space-x-3 py-8">
                    <ion-icon name="add-circle-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Ingreso Habilitación</span>
                </button>
                <button onclick="mostrarPanel('panel-editar', this)" class="tab-boton flex items-center justify-center space-x-3 py-8">
                    <ion-icon name="create-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Actualizar / Eliminar</span>
                </button>
                <button onclick="mostrarPanel('panel-listado', this)" class="tab-boton flex items-center justify-center space-x-3 py-8">
                    <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                    <span class="font-medium text-lg">Listados Varios</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================== --><!-- CONTENIDO PRINCIPAL (PANELES)                --><!-- ============================================== --><main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Panel 1: Ingreso (R2) --><div id="panel-ingreso" class="tab-panel activo">
                <form class="bg-white rounded-xl shadow-lg p-6 sm:p-10" action="{{ route('habilitacion.ingreso') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-900">Ingreso de Habilitación Profesional</h1>
                        <p class="text-sm text-gray-500">Ingrese los datos solicitados (R2).</p>
                    </div>

                    <!-- Bloques de Error/Éxito (Sin cambios) -->@if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                            <strong class="font-bold">¡Error de Validación!</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Grid de Formulario (Campos Base - Layout Corregido) --><div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Columna 1 (Rut y Semestre) --><div class="space-y-6">
                            <div class="ruts">
                                <label for="rut_al" class="block text-sm font-medium text-gray-700">RUT Alumno</label>
                                <input type="text" id="rut_al" name="rut_al" readonly required placeholder="Seleccionar Rut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <div class="lista_alumno" id="lista_alumno"></div>
                            </div>

                            <div>
                                <label for="sem_ini" class="block text-sm font-medium text-gray-700">Semestre Inicio</label>
                                <div class="flex space-x-2 mt-1">
                                    <select name="anio_ini" id="anio_ini" required class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="" disabled selected>Año</option>
                                        @for ($i = 2025; $i <= 2030; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <select name="sem_ini" id="sem_ini" required class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="" disabled selected>Semestre</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Columna 2 (Nombre y Tipo) --><div class="space-y-6">
                            <div>
                                <label for="nombre_al" class="block text-sm font-medium text-gray-700">Nombre Alumno</label>
                                <input type="text" id="nombre_al" name="nombre_al" readonly required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>
                            
                            <!-- Selector "No-Noob" (Tipo Habilitación) --><div>
                                <label class="block text-sm font-medium text-gray-700">Tipo Habilitación</label>
                                <input type="hidden" name="tipo_hab" id="tipo_hab" value="PrIng">
                                
                                <div class="mt-1 grid grid-cols-3 w-full rounded-lg shadow-sm bg-gray-100 border border-gray-300 p-1 space-x-1">
                                    <button type="button" class="tab-hab py-3 px-2 rounded-md activo-hab" data-value="PrIng">
                                        Proyecto Ingeniería
                                    </button>
                                    <button type="button" class="tab-hab py-3 px-2 rounded-md" data-value="PrInv">
                                        Proyecto Investigación
                                    </button>
                                    <button type="button" class="tab-hab py-3 px-2 rounded-md" data-value="PrTut">
                                        Práctica Tutelada
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECCIÓN PROYECTO (PrIng / PrInv) - Contenido Completo --><div class="proyecto mt-6 pt-6 border-t border-gray-200" id="proyecto">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalles del Proyecto</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Título (R2.2) --><div class="md:col-span-2">
                                <label for="titulo_hab" class="block text-sm font-medium text-gray-700">Título Habilitación</label>
                                <input type="text" id="titulo_hab" name="titulo_hab" maxlength="150" 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>

                            <!-- Descripción (R2.3) --><div class="md:col-span-2">
                                <label for="desc_hab" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="desc_hab" name="desc_hab" rows="3" maxlength="255" 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>

                            <!-- Prof. Guía (R2.4/5) --><div class="ruts">
                                <label for="rut_pg" class="block text-sm font-medium text-gray-700">Profesor Guía</label>
                                <input type="text" id="rut_pg" name="rut_pg" readonly placeholder="Seleccionar Rut" 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <div class="lista_profesores" id="lista_pg"></div>
                            </div>
                            <div>
                                <label for="nombre_pg" class="block text-sm font-medium text-gray-700">Nombre Profesor Guía</label>
                                <input type="text" id="nombre_pg" name="nombre_pg" readonly 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>

                            <!-- Prof. Comisión (R2.6/7) --><div class="ruts">
                                <label for="rut_pc" class="block text-sm font-medium text-gray-700">Profesor Comisión</label>
                                <input type="text" id="rut_pc" name="rut_pc" readonly placeholder="Seleccionar Rut" 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <div class="lista_profesores" id="lista_pc"></div>
                            </div>
                            <div>
                                <label for="nombre_pc" class="block text-sm font-medium text-gray-700">Nombre Profesor Comisión</label>
                                <input type="text" id="nombre_pc" name="nombre_pc" readonly 
                                       class="req-proyecto mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>

                            <!-- Prof. Co-Guía (R2.8/9) - OPCIONAL --><div class="ruts">
                                <label for="rut_pcg" class="block text-sm font-medium text-gray-700">Profesor Co-Guía (Opcional)</label>
                                <input type="text" id="rut_pcg" name="rut_pcg" readonly placeholder="Seleccionar Rut (Opcional)" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <div class="lista_profesores" id="lista_pcg"></div>
                            </div>
                            <div>
                                <label for="nombre_pcg" class="block text-sm font-medium text-gray-700">Nombre Co-Guía</label>
                                <input type="text" id="nombre_pcg" name="nombre_pcg" readonly 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECCIÓN PRACTICA (PrTut) - Contenido Completo --><div class="practica mt-6 pt-6 border-t border-gray-200" id="practica">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalles de la Práctica</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Descripción (R2.3) --><div class="md:col-span-2">
                                <label for="desc_pr" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="desc_pr" name="desc_pr" rows="3" maxlength="255" 
                                       class="req-practica mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>
                            
                            <!-- Prof. Tutor (R2.13/14) --><div class="ruts">
                                <label for="rut_ptut" class="block text-sm font-medium text-gray-700">Profesor Tutor</label>
                                <input type="text" id="rut_ptut" name="rut_ptut" readonly placeholder="Seleccionar Rut" 
                                       class="req-practica mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <div class="lista_profesores" id="lista_ptut"></div>
                            </div>
                            <div>
                                <label for="nombre_ptut" class="block text-sm font-medium text-gray-700">Nombre Profesor Tutor</label>
                                <input type="text" id="nombre_ptut" name="nombre_ptut" readonly 
                                       class="req-practica mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>
                            
                            <!-- Empresa (R2.11) --><div>
                                <label for="nombre_emp" class="block text-sm font-medium text-gray-700">Nombre Empresa</label>
                                <input type="text" id="nombre_emp" name="nombre_emp" minlength="10" maxlength="40" 
                                       class="req-practica mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            
                            <!-- Supervisor (R2.12) --><div>
                                <label for="nombre_sup" class="block text-sm font-medium text-gray-700">Nombre Supervisor</label>
                                <input type="text" id="nombre_sup" name="nombre_sup" minlength="10" maxlength="30" 
                                       class="req-practica mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío - (Sin cambios) --><div class="mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            Registrar Habilitación
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Panel 2: Editar (Sin cambios) --><div id="panel-editar" class="tab-panel bg-white rounded-xl shadow-lg p-8">
                <!-- Contenido de tu amigo para Actualizar/Eliminar --><h2 class="text-2xl font-bold text-gray-900 mb-4">Actualizar / Eliminar Habilitación</h2>
                <p class="text-gray-700">Aquí irá el formulario para buscar, actualizar y eliminar habilitaciones (Funcionalidad R3).</p>
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                    <p class="font-semibold">¡Recordatorio!</p>
                    <p class="text-sm">Tu amigo debe subir el código para esta sección.</p>
                </div>
            </div>

            <!-- Panel 3: Listados (Sin cambios) --><div id="panel-listado" class="tab-panel">
                <!-- Tu tabla de listado --><div class="bg-white rounded-xl shadow-lg p-6 sm:p-10">
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-900">Listado de Habilitaciones</h1>
                        <p class="text-sm text-gray-500">Visualiza todas las habilitaciones registradas.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left border-b border-gray-200">RUT Alumno</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Nombre Alumno</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Tipo</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Título / Descripción</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Semestre</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Prof. Guía/Tutor</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @php
                                    // Datos de ejemplo para el listado (reemplazar con datos reales)
                                    $habilitaciones = [
                                        (object)['rut_alumno' => '12.345.678-9', 'nombre_alumno' => 'Juan Pérez', 'tipo' => 'PrIng', 'titulo_desc' => 'Desarrollo de IA para UCSC', 'semestre' => '2025-1', 'profesor' => 'Carlos Soto', 'estado' => 'Activo'],
                                        (object)['rut_alumno' => '98.765.432-1', 'nombre_alumno' => 'María García', 'tipo' => 'PrTut', 'titulo_desc' => 'Práctica en Empresa X', 'semestre' => '2024-2', 'profesor' => 'Ana López', 'estado' => 'Finalizado'],
                                        (object)['rut_alumno' => '11.222.333-4', 'nombre_alumno' => 'Pedro Ramírez', 'tipo' => 'PrInv', 'titulo_desc' => 'Estudio de Impacto Ambiental', 'semestre' => '2025-1', 'profesor' => 'Laura Mena', 'estado' => 'Pendiente'],
                                    ];
                                @endphp

                                @foreach ($habilitaciones as $hab)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ $hab->rut_alumno }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hab->nombre_alumno }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hab->tipo }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hab->titulo_desc }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hab->semestre }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hab->profesor }}</td>
                                        <td class="py-3 px-6 text-left">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                {{ $hab->estado == 'Activo' ? 'bg-green-200 text-green-800' : '' }}
                                                {{ $hab->estado == 'Finalizado' ? 'bg-blue-200 text-blue-800' : '' }}
                                                {{ $hab->estado == 'Pendiente' ? 'bg-yellow-200 text-yellow-800' : '' }}">
                                                {{ $hab->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- 
      ==============================================
      JAVASCRIPT (Sin cambios)
      ==============================================
    --><script>
        
        // --- 1. LÓGICA DE PESTAÑAS (TABS) ---
        function mostrarPanel(panelId, botonClickeado) {
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('activo');
            });
            document.querySelectorAll('.tab-boton').forEach(boton => {
                boton.classList.remove('activo');
            });
            document.getElementById(panelId).classList.add('activo');
            botonClickeado.classList.add('activo');
        }

        // --- 2. LÓGICA DE BÚSQUEDA DE ALUMNO ---
        function buscarAlumno() {
            const inputRut = document.getElementById('rut_al');
            const inputNombre = document.getElementById('nombre_al');
            const sugerenciasDiv = document.getElementById('lista_alumno');
            
            inputRut.addEventListener('click', async () => {
                const res = await fetch(`/buscar-alumno`);
                const data = await res.json();
                if (data.length > 0) {
                    sugerenciasDiv.style.display = "block";
                    sugerenciasDiv.innerHTML = data.map(a => 
                        `<div class="item" data-rut="${a.rut_alumno}" data-nombre="${a.nombre_alumno}">${a.rut_alumno} - ${a.nombre_alumno}</div>`
                    ).join('');
                }
            });
            sugerenciasDiv.addEventListener('click', (e) => {
                if (e.target.classList.contains('item')) {
                    inputRut.value = e.target.getAttribute('data-rut');
                    inputNombre.value = e.target.getAttribute('data-nombre');
                    sugerenciasDiv.style.display = "none";
                }
            });
        }

        // --- 3. LÓGICA DE BÚSQUEDA DE PROFESOR ---
        function buscarProfesor(idRut, idNombre, idLista) {
            const inputRut = document.getElementById(idRut);
            const inputNombre = document.getElementById(idNombre);
            const sugerenciasDiv = document.getElementById(idLista);
            const listaProfesores = document.querySelectorAll('.lista_profesores');

            inputRut.addEventListener('click', async () => {
                listaProfesores.forEach(l => {
                    if (l.id !== idLista) l.style.display = "none";
                });
                
                const res = await fetch(`/buscar-profesor`);
                const data = await res.json();
                if (data.length > 0) {
                    sugerenciasDiv.style.display = "block";
                    sugerenciasDiv.innerHTML = data.map(a => 
                        `<div class="item" data-rut="${a.rut_profesor}" data-nombre="${a.nombre_profesor}">${a.rut_profesor} - ${a.nombre_profesor}</div>`
                    ).join('');
                }
            });
            sugerenciasDiv.addEventListener('click', (e) => {
                if (e.target.classList.contains('item')) {
                    inputRut.value = e.target.getAttribute('data-rut');
                    inputNombre.value = e.target.getAttribute('data-nombre');
                    sugerenciasDiv.style.display = "none";
                }
            });
        }

        // --- 4. LÓGICA DE FORMULARIO DINÁMICO ---
        function mostrarHabilitacion() {
            const tipo = document.getElementById('tipo_hab').value;
            const proyecto = document.getElementById('proyecto');
            const practica = document.getElementById('practica');

            proyecto.classList.remove('mostrar');
            practica.classList.remove('mostrar');

            proyecto.querySelectorAll('input, select, textarea').forEach(i => i.removeAttribute('required'));
            practica.querySelectorAll('input, select, textarea').forEach(i => i.removeAttribute('required'));

            if (tipo === 'PrIng' || tipo === 'PrInv') {
                proyecto.classList.add('mostrar');
                proyecto.querySelectorAll('.req-proyecto').forEach(i => i.setAttribute('required', true));
            } else if (tipo === 'PrTut') {
                practica.classList.add('mostrar');
                practica.querySelectorAll('.req-practica').forEach(i => i.setAttribute('required', true));
            }
        }
        
        // --- 5. Ocultar listas de búsqueda ---
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ruts')) {
                document.querySelectorAll('.lista_alumno, .lista_profesores').forEach(l => {
                    l.style.display = "none";
                });
            }
        });
        
        // --- 6. LÓGICA PARA EL SELECTOR "No-Noob" ---
        function inicializarTabsHabilitacion() {
            const botonesTipoHab = document.querySelectorAll('.tab-hab');
            const hiddenInputTipoHab = document.getElementById('tipo_hab');

            botonesTipoHab.forEach(boton => {
                boton.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    
                    botonesTipoHab.forEach(b => b.classList.remove('activo-hab'));
                    boton.classList.add('activo-hab');
                    
                    const valor = boton.dataset.value;
                    hiddenInputTipoHab.value = valor;
                    
                    mostrarHabilitacion();
                });
            });
        }
        
        // --- INICIALIZADORES ---
        buscarAlumno();
        buscarProfesor('rut_pg', 'nombre_pg', 'lista_pg');
        buscarProfesor('rut_pc', 'nombre_pc', 'lista_pc');
        buscarProfesor('rut_pcg', 'nombre_pcg', 'lista_pcg');
        buscarProfesor('rut_ptut', 'nombre_ptut', 'lista_ptut');
        
        inicializarTabsHabilitacion(); 
        
        document.addEventListener('DOMContentLoaded', () => {
             mostrarPanel('panel-ingreso', document.querySelector('.tab-boton.activo'));
             mostrarHabilitacion(); 
        });
        
    </script>
</body>
</html>