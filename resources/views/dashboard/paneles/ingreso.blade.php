<div id="panel-ingreso" class="tab-panel activo">  
    <form class="bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-gray-200" action="{{ route('habilitacion.ingreso') }}" method="POST">
                    @csrf
                    
                    <!-- Encabezado del formulario -->
                    <div class="mb-8 pb-4 border-b-2 border-gray-300">
                        <h1 class="text-2xl font-bold text-gray-900">Ingreso de Habilitación Profesional</h1>
                        <p class="text-sm text-gray-500">Complete todos los campos requeridos para registrar una habilitación</p>
                    </div>

                    <!-- Bloque de notificaciones: Éxito y Errores de validación -->
                    @if(session('success'))
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna 1: Identificación del Alumno y Período Académico -->
                        <div class="space-y-6">
                            <div class="ruts">
                                <label for="rut_al" class="block text-sm font-medium text-gray-700">RUT Alumno</label>
                                <input type="text" id="rut_al" name="rut_al" readonly required placeholder="Seleccionar Rut" class="mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-basefocus:border-red-500 focus:ring-red-500 p-2">
                                <div class="lista_alumno" id="lista_alumno"></div>
                            </div>

                            <div>
                                <label for="semestre_compuesto" class="block text-base font-medium text-gray-700">
                                Semestre de Inicio
                                </label>

                                <select name="semestre_compuesto" id="semestre_compuesto" required
                                    class="mt-1 block w-full rounded-xl border border-gray-300 p-3 shadow-sm text-base focus:border-red-500 focus:ring-red-500">

                                    <option value="" disabled selected>Seleccione semestre</option>

                                    @php
                                        $year = now()->year;
                                        $opciones = [
                                            $year . '-1',
                                            $year . '-2',
                                            ($year + 1) . '-1',
                                            ($year + 1) . '-2',
                                        ];
                                    @endphp

                                    @foreach ($opciones as $sem)
                                        <option value="{{ $sem }}">{{ $sem }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Columna 2: Información del Alumno y Tipo de Habilitación -->
                        <div class="space-y-6">
                            <div>
                                <label for="nombre_al" class="block text-sm font-medium text-gray-700">Nombre Alumno</label>
                                <input type="text" id="nombre_al" name="nombre_al" readonly required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 focus:border-red-500 focus:ring-red-500">
                            </div>
                            
                            <!-- Selector de Tipo de Habilitación -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo Habilitación</label>
                                <!-- Campo oculto que almacena el tipo de habilitación seleccionado -->
                                <input type="hidden" name="tipo_hab" id="tipo_hab" value="PrIng">
                                
                                <div class="mt-2 grid grid-cols-3 w-full rounded-xl shadow-md bg-gray-100 border-2 border-gray-300 p-2 gap-2">
                                    <button type="button" class="tab-hab py-3 px-4 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-100 transition" data-value="PrIng">
                                        Proyecto Ingeniería
                                    </button>
                                    <button type="button" class="tab-hab py-3 px-4 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-100 transition" data-value="PrInv">
                                        Proyecto Investigación
                                    </button>
                                    <button type="button" class="tab-hab py-3 px-4 rounded-lg text-sm font-semibold text-gray-700 hover:bg-red-100 transition" data-value="PrTut">
                                        Práctica Tutelada
                                    </button>
                                </div>
                            </div>
                        </div>
        </div>

        {{-- SUBPANELES --}}
        @include('dashboard.paneles.sub_proyecto')
        @include('dashboard.paneles.sub_practica')

        <div class="mt-8 pt-6 border-t border-gray-200">
            <button type="button" onclick="confirmarRegistro()"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-md">
                Registrar Habilitación
            </button>
        </div>

    </form>
</div>
