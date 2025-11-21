<div id="panel-listado" class="tab-panel">

    <div class="bg-white rounded-xl shadow-lg p-6 sm:p-10">
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Listado de Habilitaciones</h1>
            <p class="text-sm text-gray-500">Vista completa de todas las habilitaciones</p>
        </div>

        @php
            use App\Models\Habilitacion;

            // Entradas desde la URL
            $tipoListado    = request('tipo_listado');        // "semestral" | "historico" | null
            $semestreInicio = request('semestre_inicio');     // ej: 2025-1
            $rutProfesor    = request('rut_profesor');        // rut sin puntos ni guion

            $mensajeFiltro  = null;
            $habilitaciones = collect();

            // ------------------------------
            // Validación de tipo_listado (R4.5 / R4.15)
            // ------------------------------
            if (!is_null($tipoListado) && $tipoListado !== '') {
                $valoresPermitidos = ['semestral', 'historico'];
                if (!in_array($tipoListado, $valoresPermitidos, true)) {
                    // Valor no válido para tipo_listado
                    $mensajeFiltro  = 'El tipo de listado seleccionado no es válido.';
                    $tipoListado    = null; // anulamos para no ejecutar consultas
                    $habilitaciones = collect();
                }
            }

            if ($tipoListado === 'semestral') {
                // Semestres válidos permitidos (R2.10 acotado)
                $semestresValidos = ['2025-1', '2025-2', '2026-1', '2026-2'];

                // R4.16.1: semestre_inicio obligatorio
                if (empty($semestreInicio)) {
                    $mensajeFiltro = 'Debe ingresar el semestre de inicio.';
                } elseif (!in_array($semestreInicio, $semestresValidos, true)) {
                    // No está dentro de los valores permitidos
                    $mensajeFiltro = 'El semestre ingresado no es válido. Solo se permiten 2025-1, 2025-2, 2026-1 y 2026-2.';
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
        // ---------------------------------------
        // Validación de formato de RUT (R1.4)
        // ---------------------------------------
        $regexRut = '/^[0-9]{7,8}[0-9K]$/';

        if (!preg_match($regexRut, $rutProfesor)) {
            $mensajeFiltro = 'El RUT ingresado no es válido. Debe tener entre 8 y 9 caracteres, sin puntos ni guion, y terminar en un dígito o K.';
        } else {
            // Filtro por profesor 
            $habilitaciones = Habilitacion::with(['alumno', 'profesores'])
                ->whereHas('profesores', function ($q) use ($rutProfesor) {
                    $q->where('profesor.rut_profesor', $rutProfesor);
                })
                ->get();

            // R4.18: orden por nombre_profesor y semestre_inicio
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
}
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

            <div id="filtro-semestral"
                 style="display: {{ $tipoListado === 'semestral' ? 'block' : 'none' }};">
                <label class="block text-xs font-semibold text-gray-600">Semestre de inicio</label>
                <input type="text" name="semestre_inicio" value="{{ $semestreInicio }}"
                       placeholder="2025-1 / 2025-2 / 2026-1 / 2026-2"
                       class="mt-1 block w-40 rounded-lg border border-gray-300 p-2 text-sm">
            </div>

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
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $hab->rut_alumno }}
                            </td>

                            <td class="py-3 px-6 text-left">
                                {{ $hab->alumno->nombre_alumno ?? 'Sin nombre' }}
                            </td>

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
        setTimeout(function () {
            const tabListado = document.querySelector('[data-tab="listado"]');
            if (tabListado) {
                tabListado.click();
            }
        }, 0);
    }

});
</script>
