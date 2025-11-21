<div id="panel-listado" class="tab-panel">

    <div class="bg-white rounded-xl shadow-lg p-6 sm:p-10">
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Listado de Habilitaciones</h1>
            <p class="text-sm text-gray-500">Vista completa de todas las habilitaciones</p>
        </div>

        @php
            use App\Models\Habilitacion;

           
            $tipoListado    = request('tipo_listado');        
            $semestreInicio = request('semestre_inicio');     
            $rutProfesor    = request('rut_profesor');        

            $mensajeFiltro  = null;                           
            $habilitaciones = collect();                     

            if ($tipoListado === 'semestral') {
                // R4.16.1: semestre_inicio obligatorio
                if (empty($semestreInicio)) {
                    $mensajeFiltro = 'Debe ingresar el semestre de inicio.';
                } else {
                    // R4.16.1.1: filtrar por semestre_inicio y ordenar por semestre_inicio
                    $habilitaciones = Habilitacion::with(['alumno', 'profesores'])
                        ->where('semestre_inicio', $semestreInicio)
                        ->orderBy('semestre_inicio')
                        ->get();

                    if ($habilitaciones->isEmpty()) {
                        // Mensaje requerido cuando no hay registros
                        $mensajeFiltro = 'No se encontraron registros para el filtro aplicado.';
                    }
                }
            } elseif ($tipoListado === 'historico') {
                // R4.17: rut_profesor obligatorio
                if (empty($rutProfesor)) {
                    $mensajeFiltro = 'Debe ingresar el RUT del profesor.';
                } else {
                    // Filtramos por profesor
                    $habilitaciones = Habilitacion::with(['alumno', 'profesores'])
                        ->whereHas('profesores', function ($q) use ($rutProfesor) {
                            $q->where('rut_profesor', $rutProfesor);
                        })
                        ->get();

                    // R4.18: ordenar por nombre_profesor y semestre_inicio
                    $habilitaciones = $habilitaciones->sortBy(function ($hab) {
                        $prof   = $hab->profesores->first();
                        $nombre = $prof->nombre_profesor ?? '';
                        return $nombre . ' ' . $hab->semestre_inicio;
                    });

                    if ($habilitaciones->isEmpty()) {
                        $mensajeFiltro = 'No se encontraron registros para el filtro aplicado.';
                    }
                }
            }
            // Si no hay tipoListado, se deja la colección vacía y no se muestran filas todavía
        @endphp

        {{-- Filtros R4.16 / R4.17 --}}
        <form method="GET" action="{{ url()->current() }}" class="mb-4 flex flex-wrap gap-4 items-end">

            {{-- Tipo de listado --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600">Tipo de listado</label>
                <select name="tipo_listado"
                        class="mt-1 block w-40 rounded-lg border border-gray-300 p-2 text-sm">
                    <option value="">Seleccione…</option>
                    <option value="semestral" {{ $tipoListado === 'semestral' ? 'selected' : '' }}>Semestral</option>
                    <option value="historico" {{ $tipoListado === 'historico' ? 'selected' : '' }}>Histórico</option>
                </select>
            </div>

            {{-- Semestre (solo si tipo_listado = semestral) --}}
            <div id="filtro-semestral"
                 style="display: {{ $tipoListado === 'semestral' ? 'block' : 'none' }};">
                <label class="block text-xs font-semibold text-gray-600">Semestre de inicio</label>
                <input type="text" name="semestre_inicio" value="{{ $semestreInicio }}"
                       placeholder="AAAA-1 / AAAA-2"
                       class="mt-1 block w-32 rounded-lg border border-gray-300 p-2 text-sm">
            </div>

            {{-- RUT Profesor (solo si tipo_listado = historico) --}}
            <div id="filtro-historico"
                 style="display: {{ $tipoListado === 'historico' ? 'block' : 'none' }};">
                <label class="block text-xs font-semibold text-gray-600">RUT Profesor</label>
                <input type="text" name="rut_profesor" value="{{ $rutProfesor }}"
                       placeholder="Sin puntos ni guion"
                       class="mt-1 block w-40 rounded-lg border border-gray-300 p-2 text-sm">
            </div>

            <div>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow">
                    Aplicar filtro
                </button>
            </div>
        </form>

        @if ($mensajeFiltro)
            <div class="mb-4 bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded text-sm">
                {{ $mensajeFiltro }}
            </div>
        @endif

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

                            <td class="py-3 px-6 text-left">
                                {{ $hab->descripcion }}
                            </td>

                            <td class="py-3 px-6 text-left">
                                {{ $hab->semestre_inicio }}
                            </td>

                            <td class="py-3 px-6 text-left">
                                {{ optional($hab->profesores->first())->nombre_profesor ?? 'No asignado' }}
                            </td>

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
                                @if ($tipoListado)
                                    {{ $mensajeFiltro ?? 'No se encontraron registros para el filtro aplicado.' }}
                                @else
                                    Seleccione un tipo de listado y aplique el filtro para ver resultados.
                                @endif
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* --------------------------
       1) Mostrar / ocultar filtros
       -------------------------- */
    const selectTipo = document.querySelector('#panel-listado select[name="tipo_listado"]');
    const bloqueSem  = document.getElementById('filtro-semestral');
    const bloqueHist = document.getElementById('filtro-historico');

    function actualizarFiltros() {
        if (!selectTipo) return;

        const v = selectTipo.value;

        if (v === 'semestral') {
            bloqueSem.style.display  = 'block';
            bloqueHist.style.display = 'none';
        } else if (v === 'historico') {
            bloqueSem.style.display  = 'none';
            bloqueHist.style.display = 'block';
        } else {
            bloqueSem.style.display  = 'none';
            bloqueHist.style.display = 'none';
        }
    }

    if (selectTipo) {
        selectTipo.addEventListener('change', actualizarFiltros);
        actualizarFiltros();
    }

    /* --------------------------
       2) Mantener pestaña Listado
       -------------------------- */
    const filtroAplicado = "{{ request('tipo_listado') ? '1' : '0' }}" === "1";

    if (filtroAplicado) {
        // ESTE ES EL BOTÓN CORRECTO SEGÚN TU INICIO.BLADE.PHP REAL
        const tabListado = document.querySelector('[data-tab="listado"]');

        if (tabListado) {
            tabListado.click();
        }
    }

});
</script>

