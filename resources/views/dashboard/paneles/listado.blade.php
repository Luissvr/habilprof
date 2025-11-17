<div id="panel-listado" class="tab-panel">

    <div class="bg-white rounded-xl shadow-lg p-6 sm:p-10">
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Listado de Habilitaciones</h1>
            <p class="text-sm text-gray-500">Vista completa de todas las habilitaciones</p>
        </div>

        <div class="overflow-x-auto">
             <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left border-b border-gray-200">RUT Alumno</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Nombre Alumno</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Tipo de Habilitación</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Título / Descripción</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Período Académico</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Profesor Responsable</th>
                                    <th class="py-3 px-6 text-left border-b border-gray-200">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @php
                                    /**
                                     * DATOS DE EJEMPLO - Reemplazar con datos desde BD
                                     * Estructura: array de objetos con información de habilitaciones
                                     */
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
