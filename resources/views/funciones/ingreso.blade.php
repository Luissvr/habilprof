<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabilProf UCSC</title>
    <link rel="stylesheet" href="{{ asset('efunciones/styles.css') }}">
</head>
<body>
    <div class="contenedor">
        <div class="titulo"> 
            <h1>Ingreso de Habilitacion Profesional</h1>
            <p> Ingrese los datos solicitados </p>
        </div>
        
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form class="formulario" action="{{ route('habilitacion.ingreso') }}" method="POST">
            @csrf
            <h2> Datos Habilitación </h2>
            
            <div class="campo">
                <div class=ruts>
                    <label for="rut_al" class="obligatorio"> Rut alumno </label>
                    <input type="text" id="rut_al" name="rut_al" readonly required placeholder="Seleccionar Rut">
                    <div class="lista_alumno" id="lista_alumno"></div>
                </div>

                <label for="nombre_al" class="obligatorio"> Nombre Alumno </label>
                <input type="text" id="nombre_al"name="nombre_al" readonly required>

                <label for="sem_ini" class="obligatorio" > Semestre Inicio </label>
                <div class="semestre_ini">
                    <select name="anio_ini" id="anio_ini" required>
                        <option value="anio" disabled selected> Año </option>
                        @for ($i = 2025; $i <= 2030; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>

                    <select name="sem_ini" id="sem_ini" required>
                        <option value="semestre" disabled selected> Semestre </option>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                    </select>
                </div>
                
                <label for="tipo_hab" class="obligatorio"> Tipo Habilitacion </label>
                <div class="tipo_habi">
                    <select name="tipo_hab" id="tipo_hab" required>
                        <option value="seleccion" disabled selected> Tipo Habilitación </option>
                        <option value="PrIng"> Proyecto de Ingeniería </option>
                        <option value="PrInv"> Proyecto de Investigación </option>
                        <option value="PrTut"> Practica Tutelada </option>
                    </select>
                </div>
                
                <div class="proyecto" id="proyecto">
                    
                    <label for="titulo_hab" class="obligatorio"> Titulo Habilitación </label>
                    <input type="text" id="titulo_hab" name="titulo_hab" maxlength="150" class="req-proyecto">

                    <label for="desc" class="obligatorio"> Descripción </label>
                    <input type="text" id="desc_hab" name="desc_hab" maxlength="255" class="req-proyecto">

                    <div class=ruts>
                        <label for="rut_pg" class="obligatorio"> Rut profesor guía </label>
                        <input type="text" id="rut_pg" name="rut_pg" readonly placeholder="Seleccionar Rut Profesor Guía" class="req-proyecto">
                        <div class="lista_profesores" id="lista_pg"></div>
                    </div>

                    <label for="nombre_pg" class="obligatorio"> Nombre profesor guía </label>
                    <input type="text" id="nombre_pg" name="nombre_pg" readonly class="req-proyecto">

                    <div class=ruts>
                        <label for="rut_pc" class="obligatorio"> Rut profesor comisión </label>
                        <input type="text" id="rut_pc"  name="rut_pc" readonly placeholder="Seleccionar Rut Profesor Comisión" class="req-proyecto">
                        <div class="lista_profesores" id="lista_pc"></div>
                    </div>
                    
                    <label for="nombre_pc" class="obligatorio"> Nombre profesor comisión </label>
                    <input type="text" id="nombre_pc"  name="nombre_pc"readonly class="req-proyecto">

                    <div class=ruts>
                        <label for="rut_pcg"> Rut profesor co-guía </label>
                        <input type="text" id="rut_pcg" name="rut_pcg" readonly placeholder="Seleccionar Rut Profesor Co-Guía">
                        <div class="lista_profesores" id="lista_pcg"></div>
                    </div>
                    
                    <label for="nombre_pcg"> Nombre profesor co-guía </label>
                    <input type="text" id="nombre_pcg"  name="nombre_pcg" readonly>
                </div>

                <div class="practica" id="practica">
                    <label for="desc" class="obligatorio"> Descripción </label>
                    <input type="text" id="desc_pr" name ="desc_pr" maxlength="255" class="req-practica">

                    <div class=ruts>
                        <label for="rut_ptut" class="obligatorio"> Rut profesor tutor </label>
                        <input type="text" id="rut_ptut" name="rut_ptut" readonly placeholder="Seleccionar Rut Profesor Tutor" class="req-practica">
                        <div class="lista_profesores" id="lista_ptut"></div>
                    </div>
                    
                    <label for="nombre_ptut" class="obligatorio"> Nombre profesor tutor </label>
                    <input type="text" id="nombre_ptut" name="nombre_ptut" readonly class="req-practica">

                    <label for="nombre_emp" class="obligatorio"> Nombre empresa </label>
                    <input type="text" id="nombre_emp" name="nombre_emp" minlength="10" maxlength="40" class="req-practica">

                    <label for="nombre_sup" class="obligatorio"> Nombre supervisor </label>
                    <input type="text" id="nombre_sup" name="nombre_sup" minlength="10" maxlength="30" class="req-practica">
                </div>
            </div>
            <button type="submit" class="boton">Registrar Habilitación </button>
            
            </form>
    </div>

    <script>
        function buscarAlumno(){
            // ... (Tu código de buscarAlumno es perfecto, no se toca) ...
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
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.ruts')) { // <-- Corregí esto, 'container' no existía
                    sugerenciasDiv.style.display = "none";
                }
            });
        }
        
        function mostrarHabilitacion() {
            const tipo = document.getElementById('tipo_hab').value;
            const proyecto = document.getElementById('proyecto');
            const practica = document.getElementById('practica');

            // Oculta ambas secciones
            proyecto.classList.remove('mostrar');
            practica.classList.remove('mostrar');

            // Quita 'required' de todos los campos en AMBAS secciones
            // (Tu lógica original estaba bien)
            proyecto.querySelectorAll('[required]').forEach(i => i.removeAttribute('required'));
            practica.querySelectorAll('[required]').forEach(i => i.removeAttribute('required'));

            // Muestra y vuelve a poner 'required' SOLO a los campos con la clase
            if (tipo === 'PrIng' || tipo === 'PrInv') {
                proyecto.classList.add('mostrar');
                // --- ¡CORRECCIÓN AQUÍ! ---
                // Solo selecciona los que tienen la clase 'req-proyecto'
                proyecto.querySelectorAll('.req-proyecto').forEach(i => i.setAttribute('required', true));
            } else if (tipo === 'PrTut') {
                practica.classList.add('mostrar');
                // --- ¡CORRECCIÓN AQUÍ! ---
                // Solo selecciona los que tienen la clase 'req-practica'
                practica.querySelectorAll('.req-practica').forEach(i => i.setAttribute('required', true));
            }
        }
        
        function buscarProfesor(idRut, idNombre, idLista) {
            // ... (Tu código de buscarProfesor es perfecto, no se toca) ...
            const inputRut = document.getElementById(idRut);
            const inputNombre = document.getElementById(idNombre);
            const sugerenciasDiv = document.getElementById(idLista);

            inputRut.addEventListener('click', async () => {
                const res = await fetch(`/buscar-profesor`);
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
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.ruts')) { // <-- Corregí esto, 'campo' era muy genérico
                    sugerenciasDiv.style.display = "none";
                }
            });
        }
        
        // --- INICIALIZADORES (Tu código estaba perfecto) ---
        buscarAlumno();
        buscarProfesor('rut_pg', 'nombre_pg', 'lista_pg');
        buscarProfesor('rut_pc', 'nombre_pc', 'lista_pc');
        buscarProfesor('rut_pcg', 'nombre_pcg', 'lista_pcg');
        buscarProfesor('rut_ptut', 'nombre_ptut', 'lista_ptut');
        
        // Ejecuta la función al cargar la página (para ocultar todo)
        mostrarHabilitacion(); 
        // Ejecuta la función cada vez que el <select> cambie
        document.getElementById('tipo_hab').addEventListener('change', mostrarHabilitacion);
    </script>
</body>
</html>