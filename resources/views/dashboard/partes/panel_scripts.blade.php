<script>
// Tabs principales
function mostrarPanel(id, boton) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('activo'));
    document.getElementById(id).classList.add('activo');

    document.querySelectorAll('.tab-boton').forEach(b => b.classList.remove('activo'));
    boton.classList.add('activo');
}

// Tipo habilitación
document.querySelectorAll('.tab-hab').forEach(btn => {
    btn.addEventListener('click', () => {

        document.querySelectorAll('.tab-hab').forEach(t => t.classList.remove('activo-hab'));
        btn.classList.add('activo-hab');

        const valor = btn.dataset.value;
        document.getElementById('tipo_hab').value = valor;

        document.getElementById('proyecto').style.display = 'none';
        document.getElementById('practica').style.display = 'none';

        if (valor === 'PrTut') {
            document.getElementById('practica').style.display = 'block';
        } else {
            document.getElementById('proyecto').style.display = 'block';
        }
    });
});
</script>
