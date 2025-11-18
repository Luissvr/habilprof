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

    @forelse ($habilitaciones as $hab)
        <tr class="border-b border-gray-200 hover:bg-gray-50">
            {{-- RUT --}}
            <td class="py-3 px-6 text-left whitespace-nowrap">
                {{ $hab->rut_alumno }}
            </td>

            {{-- Nombre alumno (relación) --}}
            <td class="py-3 px-6 text-left">
                {{ $hab->alumno->nombre_alumno ?? 'Sin nombre' }}
            </td>

            {{-- Tipo de habilitación --}}
            <td class="py-3 px-6 text-left">
                @switch($hab->t_habilitacion)
                    @case('PrIng') Proyecto Ingeniería @break
                    @case('PrInv') Proyecto Investigación @break
                    @case('PrTut') Práctica Tutelada @break
                    @default {{ $hab->t_habilitacion }}
                @endswitch
            </td>

            {{-- Título / descripción --}}
            <td class="py-3 px-6 text-left">
                {{ $hab->descripcion }}
            </td>

            {{-- Período académico --}}
            <td class="py-3 px-6 text-left">
                {{ $hab->semestre_inicio }}
            </td>

            {{-- Profesor responsable (primer profesor asociado) --}}
            <td class="py-3 px-6 text-left">
                {{ optional($hab->profesores->first())->nombre_profesor ?? 'No asignado' }}
            </td>

            {{-- Estado (ejemplo: según si tiene nota o no) --}}
            <td class="py-3 px-6 text-left">
                @php
                    $estado = is_null($hab->nota) ? 'Pendiente' : 'Finalizado';
                @endphp
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ $estado == 'Pendiente' ? 'bg-yellow-200 text-yellow-800' : '' }}
                    {{ $estado == 'Finalizado' ? 'bg-blue-200 text-blue-800' : '' }}">
                    {{ $estado }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                No hay habilitaciones registradas.
            </td>
        </tr>
    @endforelse

</tbody>
                        </table>
        </div>
    </div>

</div>
