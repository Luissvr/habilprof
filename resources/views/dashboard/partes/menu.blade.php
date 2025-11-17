<div class="bg-red-600 shadow-lg sticky top-16 z-30">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-3">

            {{-- BOTÓN INGRESO --}}
            <button onclick="mostrarPanel('panel-ingreso', this)" 
                class="tab-boton activo flex items-center justify-center space-x-3 py-8 text-white">
                <ion-icon name="add-circle-outline" class="text-2xl"></ion-icon>
                <span class="font-medium text-lg">Ingreso Habilitación</span>
            </button>

            {{-- BOTÓN EDITAR --}}
            <button onclick="mostrarPanel('panel-editar', this)" 
                class="tab-boton flex items-center justify-center space-x-3 py-8 text-white">
                <ion-icon name="create-outline" class="text-2xl"></ion-icon>
                <span class="font-medium text-lg">Actualizar / Eliminar</span>
            </button>

            {{-- BOTÓN LISTADO --}}
            <button onclick="mostrarPanel('panel-listado', this)" 
                class="tab-boton flex items-center justify-center space-x-3 py-8 text-white">
                <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                <span class="font-medium text-lg">Listados Varios</span>
            </button>

        </div>
    </div>
</div>
