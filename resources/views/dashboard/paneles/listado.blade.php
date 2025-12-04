{{-- Filtros de Listados Varios --}}

@if (!empty($mensajeFiltro))
    <div class="mb-4 px-4 py-3 rounded-lg bg-yellow-100 border border-yellow-300 text-sm text-yellow-800">
        {{ $mensajeFiltro }}
    </div>
@endif

<form method="GET" action="{{ route('dashboard.inicio_listados') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    {{-- Tipo de Listado --}}
    <div class="md:col-span-1">
        <label for="tipo_listado" class="block font-semibold mb-1">Tipo de Listado</label>
        <select id="tipo_listado" name="tipo_listado"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">Seleccione...</option>
            <option value="semestral" {{ $tipoListado === 'semestral' ? 'selected' : '' }}>Semestral</option>
            <option value="historico" {{ $tipoListado === 'historico' ? 'selected' : '' }}>Histórico</option>
        </select>
    </div>

    {{-- Semestre Inicio (solo Semestral) --}}
    <div id="campo_semestre" class="md:col-span-1 {{ $tipoListado === 'semestral' ? '' : 'hidden' }}">
        <label for="semestre_inicio" class="block font-semibold mb-1">Semestre Inicio</label>
        <input type="text"
               id="semestre_inicio"
               name="semestre_inicio"
               value="{{ old('semestre_inicio', $semestreInicio) }}"
               placeholder="2025-1"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        <p class="mt-1 text-xs text-gray-500">
            Formato: 2025-1, 2025-2, 2026-1 o 2026-2.
        </p>
    </div>

    {{-- RUT Profesor (solo Histórico) --}}
    <div id="campo_rut" class="md:col-span-1 {{ $tipoListado === 'historico' ? '' : 'hidden' }}">
        <label for="rut_profesor" class="block font-semibold mb-1">RUT Profesor</label>
        <input type="text"
               id="rut_profesor"
               name="rut_profesor"
               value="{{ old('rut_profesor', $rutProfesor) }}"
               minlength="8"
               maxlength="9"
               pattern="[0-9]{7,8}[0-9K]"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
               placeholder="Ej: 21069322K">
        <p class="mt-1 text-xs text-gray-500">
            Sin puntos ni guión, 8–9 caracteres. Ej: 21069322K.
        </p>
    </div>

    {{-- Botón --}}
    <div class="md:col-span-1 flex items-start md:mt-7">
    <button type="submit"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm text-sm">
        Aplicar filtros
    </button>
</div>

</form>

{{-- ========================= --}}
{{--   RESULTADOS: SEMESTRAL   --}}
{{-- ========================= --}}
@if ($tipoListado === 'semestral')

    {{-- LISTA: Proyectos (PrIng / PrInv) --}}
    <h2 class="text-lg font-semibold mt-2 mb-2">
        Proyectos de Ingeniería / Investigación
    </h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-2">RUT Alumno</th>
                    <th class="px-4 py-2">Nombre Alumno</th>
                    <th class="px-4 py-2">Tipo Hab.</th>
                    <th class="px-4 py-2">Semestre</th>
                    <th class="px-4 py-2">Título Proyecto / Investigación</th>
                    <th class="px-4 py-2">Descripción</th>
                    <th class="px-4 py-2">Profesor Guía</th>
                    <th class="px-4 py-2">Profesor Comisión</th>
                    <th class="px-4 py-2">Profesor Co-guía</th>
                    <th class="px-4 py-2">Nota</th>
                    <th class="px-4 py-2">Fecha Nota</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($listaProyectos as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['rut_alumno'] }}</td>
                        <td class="px-4 py-2">{{ $item['nombre_alumno'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['tipo_habilitacion'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['semestre_inicio'] }}</td>
                        <td class="px-4 py-2">{{ $item['titulo_proyecto'] }}</td>
                        <td class="px-4 py-2">{{ $item['descripcion'] }}</td>
                        <td class="px-4 py-2">{{ $item['profesor_guia'] }}</td>
                        <td class="px-4 py-2">{{ $item['profesor_comision'] }}</td>
                        <td class="px-4 py-2">{{ $item['profesor_coguia'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['nota'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['fecha_registro_nota'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron proyectos para el semestre indicado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- LISTA: Prácticas (PrTut) --}}
    <h2 class="text-lg font-semibold mt-8 mb-2">
        Prácticas Tuteladas
    </h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-2">RUT Alumno</th>
                    <th class="px-4 py-2">Nombre Alumno</th>
                    <th class="px-4 py-2">Tipo Hab.</th>
                    <th class="px-4 py-2">Semestre</th>
                    <th class="px-4 py-2">Empresa</th>
                    <th class="px-4 py-2">Supervisor Empresa</th>
                    <th class="px-4 py-2">Profesor Tutor</th>
                    <th class="px-4 py-2">Nota</th>
                    <th class="px-4 py-2">Fecha Nota</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($listaPracticas as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['rut_alumno'] }}</td>
                        <td class="px-4 py-2">{{ $item['nombre_alumno'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['tipo_habilitacion'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['semestre_inicio'] }}</td>
                        <td class="px-4 py-2">{{ $item['empresa'] }}</td>
                        <td class="px-4 py-2">{{ $item['supervisor_empresa'] }}</td>
                        <td class="px-4 py-2">{{ $item['profesor_tutor'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['nota'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['fecha_registro_nota'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron prácticas para el semestre indicado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endif

{{-- ========================= --}}
{{--   RESULTADOS: HISTÓRICO   --}}
{{-- ========================= --}}
@if ($tipoListado === 'historico')

    <h2 class="text-lg font-semibold mt-2 mb-2">
        Listado Histórico de Habilitaciones
    </h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-2">Profesor</th>
                    <th class="px-4 py-2">Semestre</th>
                    <th class="px-4 py-2">RUT Alumno</th>
                    <th class="px-4 py-2">Nombre Alumno</th>
                    <th class="px-4 py-2">Tipo Hab.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($listaHistorico as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item['nombre_profesor'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['semestre_inicio'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['rut_alumno'] }}</td>
                        <td class="px-4 py-2">{{ $item['nombre_alumno'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $item['tipo_habilitacion'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron registros para el filtro aplicado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endif

{{-- ========================= --}}
{{--   JS: filtros dinámicos   --}}
{{-- ========================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipo   = document.getElementById('tipo_listado');
    const campoSemestre = document.getElementById('campo_semestre');
    const campoRut      = document.getElementById('campo_rut');
    const rutInput      = document.getElementById('rut_profesor');

    function actualizarFiltros() {
        const tipo = selectTipo.value;

        campoSemestre.classList.add('hidden');
        campoRut.classList.add('hidden');

        if (tipo === 'semestral') {
            campoSemestre.classList.remove('hidden');
        } else if (tipo === 'historico') {
            campoRut.classList.remove('hidden');
        }
    }

    if (selectTipo) {
        actualizarFiltros();
        selectTipo.addEventListener('change', actualizarFiltros);
    }

    // Limitar RUT profesor: sólo 0-9 y K, máx 9 caracteres
    if (rutInput) {
        rutInput.addEventListener('input', function () {
            let v = this.value.toUpperCase();
            v = v.replace(/[^0-9K]/g, '');   // solo números y K
            if (v.length > 9) {
                v = v.slice(0, 9);
            }
            this.value = v;
        });
    }
});
</script>
