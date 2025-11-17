<div class="practica mt-6 pt-6 border-t border-gray-200" id="practica" style="display:none">

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información de la Práctica Tutelada</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Descripción --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Descripción de la Práctica</label>
            <textarea id="desc_pr" name="desc_pr" rows="3" maxlength="255"
                class="req-practica mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2"></textarea>
        </div>

        {{-- Profesor Tutor --}}
        <div class="ruts">
            <label class="block text-sm font-medium text-gray-700">Profesor Tutor *</label>
            <input type="text" id="rut_ptut" name="rut_ptut" readonly placeholder="Seleccionar Rut"
                class="req-practica mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
            <div class="lista_profesores" id="lista_ptut"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Profesor Tutor</label>
            <input type="text" id="nombre_ptut" name="nombre_ptut" readonly
                class="req-practica mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

        {{-- Empresa --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Empresa *</label>
            <input type="text" id="nombre_emp" name="nombre_emp" minlength="10" maxlength="40"
                class="req-practica mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

        {{-- Supervisor --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Supervisor *</label>
            <input type="text" id="nombre_sup" name="nombre_sup" minlength="10" maxlength="30"
                class="req-practica mt-1 block w-full rounded-lg border-2 border-gray-300 shadow-sm text-base focus:border-red-500 focus:ring-red-500 p-2">
        </div>

    </div>

</div>
