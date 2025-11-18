<div class="proyecto mt-6 pt-6 border-t border-gray-200" id="proyecto" style="display:none">

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Proyecto</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Título --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Título del Proyecto</label>
            <input type="text" id="titulo_hab" name="titulo_hab" maxlength="150"
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

        {{-- Descripción --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Descripción del Proyecto</label>
            <textarea id="desc_hab" name="desc_hab" rows="3" maxlength="255"
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2"></textarea>
        </div>

        {{-- Profesor Guía --}}
        <div class="ruts">
            <label class="block text-sm font-medium text-gray-700">Profesor Guía *</label>
            <input type="text" id="rut_pg" name="rut_pg" readonly placeholder="Seleccionar Rut"
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
            <div class="lista_profesores" id="lista_pg"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Profesor Guía</label>
            <input type="text" id="nombre_pg" name="nombre_pg" readonly
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

        {{-- Profesor Comisión --}}
        <div class="ruts">
            <label class="block text-sm font-medium text-gray-700">Profesor Comisión *</label>
            <input type="text" id="rut_pc" name="rut_pc" readonly placeholder="Seleccionar Rut"
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
            <div class="lista_profesores" id="lista_pc"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Profesor Comisión</label>
            <input type="text" id="nombre_pc" name="nombre_pc" readonly
                class="req-proyecto mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

        {{-- Co-guía --}}
        <div class="ruts">
            <label class="block text-sm font-medium text-gray-700">Profesor Co-Guía (Opcional)</label>
            <input type="text" id="rut_pcg" name="rut_pcg" readonly placeholder="Seleccionar Rut"
                class="mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
            <div class="lista_profesores" id="lista_pcg"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Co-Guía</label>
            <input type="text" id="nombre_pcg" name="nombre_pcg" readonly
                class="mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

    </div>
</div>
