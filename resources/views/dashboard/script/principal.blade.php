<script>
        /**
         * FUNCIÓN: Cambio de Paneles (Tabs)
         * Oculta todos los paneles y muestra el seleccionado
         * Tambi n actualiza el estado visual del botón activo
         * @param {string} panelId - ID del panel a mostrar
         * @param {HTMLElement} botonClickeado - Elemento botón que fue clickeado
         */
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

        /**
         * FUNCIÓN: Búsqueda de Alumnos
         * Carga lista de alumnos desde la BD y permite seleccionar uno
         * Relena RUT y nombre automáticamente
         */
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

        /**
         * FUNCIÓN: Búsqueda de Profesores
         * Carga lista de profesores desde la BD para un campo específico
         * @param {string} idRut - ID del campo de entrada para RUT
         * @param {string} idNombre - ID del campo de entrada para nombre
         * @param {string} idLista - ID del div para mostrar lista
         */
        function buscarProfesorDINF(idRut, idNombre, idLista) {
            const inputRut = document.getElementById(idRut);
            const inputNombre = document.getElementById(idNombre);
            const sugerenciasDiv = document.getElementById(idLista);
            const listaProfesores = document.querySelectorAll('.lista_profesores');

            inputRut.addEventListener('click', async () => {

            listaProfesores.forEach(l => {
                if (l.id !== idLista) l.style.display = "none";
            });
        
            const res = await fetch('/buscar-profesor-dinf');
            const data = await res.json();

            if (data.length > 0) {
                sugerenciasDiv.style.display = "block";
                sugerenciasDiv.innerHTML = data.map(a => 
                    `<div class="item" data-rut="${a.rut_profesor}" data-nombre="${a.nombre_profesor}">
                    ${a.rut_profesor} - ${a.nombre_profesor}
                    </div>`
                ).join('');
            }
        });

        sugerenciasDiv.addEventListener('click', (e) => {
            if (e.target.classList.contains('item')) {
                inputRut.value = e.target.dataset.rut;
                inputNombre.value = e.target.dataset.nombre;
                sugerenciasDiv.style.display = "none";
            }
        });
}

        function buscarProfesorTodos(idRut, idNombre, idLista) {
            const inputRut = document.getElementById(idRut);
            const inputNombre = document.getElementById(idNombre);
            const sugerenciasDiv = document.getElementById(idLista);
            const listaProfesores = document.querySelectorAll('.lista_profesores');

            inputRut.addEventListener('click', async () => {
                listaProfesores.forEach(l => {
                    if (l.id !== idLista) l.style.display = "none";
                });
                
                const res = await fetch(`/buscar-profesor-todos`);
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


        /**
         * FUNCIÓN: Control de Formulario Dinámico
         * Muestra/oculta secciones según el tipo de habilitación seleccionado
         * Establece campos como requeridos o no según corresponda
         */
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
        
        /**
         * EVENTO: Cierre de Listas Desplegables
         * Oculta las listas de búsqueda al clickear fuera de ellas
         */
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ruts')) {
                document.querySelectorAll('.lista_alumno, .lista_profesores').forEach(l => {
                    l.style.display = "none";
                });
            }
        });

        document.querySelectorAll('.tab-hab').forEach(btn => {
            btn.addEventListener('click', function () {

                // Quitar activo de todos
                document.querySelectorAll('.tab-hab').forEach(b => b.classList.remove('activo-hab'));

                // Activar el seleccionado
                this.classList.add('activo-hab');

                // Actualizar el hidden
                document.getElementById('tipo_hab').value = this.dataset.value;

                // Ocultar subpaneles
                document.getElementById('proyecto').style.display = 'none';
                document.getElementById('practica').style.display = 'none';

                // Mostrar el que corresponde
                if (this.dataset.value === 'PrIng' || this.dataset.value === 'PrInv') {
                    document.getElementById('proyecto').style.display = 'block';
                }
                if (this.dataset.value === 'PrTut') {
                    document.getElementById('practica').style.display = 'block';
                }
            });
        });

        
        /**
         * FUNCIÓN: Inicializar Selector de Tipo de Habilitación
         * Configura los tres botones para seleccionar tipo de habilitación
         * Actualiza el campo oculto y muestra sección correspondiente
         */
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
        
        /**
         * INICIALIZACIÓN
         * Ejecuta todas las funciones necesarias al cargar la página
         */
        // Iniciadores de búsqueda para alumnos
        buscarAlumno();

        // Iniciadores de búsqueda para profesores (uno por cada rol)
        buscarProfesorDINF("rut_pg", "nombre_pg", "lista_pg");     // Profesor Guía
        buscarProfesorDINF('rut_pc', 'nombre_pc', 'lista_pc');      // Profesor Comisión
        buscarProfesorTodos('rut_pcg', 'nombre_pcg', 'lista_pcg');   // Profesor Co-Guía
        buscarProfesorDINF('rut_ptut', 'nombre_ptut', 'lista_ptut'); // Profesor Tutor
        
        // Inicializador del selector de tipo de habilitación
        inicializarTabsHabilitacion(); 
        
        // Evento: Cuando el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', () => {
             // Muestra el panel de ingreso por defecto
             mostrarPanel('panel-ingreso', document.querySelector('.tab-boton.activo'));
             // Muestra los campos correspondientes según el tipo seleccionado
             mostrarHabilitacion(); 
        });
        
    </script>

    <script>
    function confirmarRegistro() {
        document.getElementById("popup-confirmacion").classList.remove("hidden");
        document.getElementById("popup-confirmacion").classList.add("flex");
    }

    function cancelarRegistro() {
        document.getElementById("popup-confirmacion").classList.add("hidden");
        document.getElementById("popup-confirmacion").classList.remove("flex");
    }

    function enviarFormulario() {
        // Ocultar popup
        cancelarRegistro();

        // Enviar formulario real
        document.querySelector('#panel-ingreso form').submit();
    }
    </script>

    <!-- POP-UP DE CONFIRMACIÓN  R2.19-->
    <div id="popup-confirmacion" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-2xl p-8 w-11/12 sm:w-96">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            Confirmación de Registro
        </h2>

        <p class="text-gray-700 mb-6">
            ¿Está seguro que desea registrar esta habilitación profesional?
        </p>

        <div class="flex justify-end space-x-3">
            <button onclick="cancelarRegistro()"
                class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-900">
                Cancelar
            </button>

            <button onclick="enviarFormulario()"
                class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold">
                Confirmar
            </button>
        </div>
    </div>
</div>