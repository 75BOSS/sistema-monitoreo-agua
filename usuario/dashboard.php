
<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header_usuario.php';


// Obtener sensores para el formulario
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Visualización de Reportes</h2>

    <form id="formComparar" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Sensor 1:</label>
            <select class="form-control" id="sensor1" name="sensor1" required>
                <option value="">Seleccione</option>
                <?php while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4" id="sensor2Container" style="display: none;">
            <label>Sensor 2 (para comparar):</label>
            <select class="form-control" id="sensor2" name="sensor2">
                <option value="">Seleccione</option>
                <?php
                $sensores->data_seek(0); // Reiniciar el puntero
                while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Tipo de Gráfica:</label>
            <select class="form-control" id="tipoGrafica" name="tipoGrafica" required>
                <option value="historica">Histórica (por sensor)</option>
                <option value="comparacion_fechas">Comparación por fechas</option>
                <option value="comparacion_temporadas">Temporada lluvia/sequía</option>
                <option value="comparacion_sensores">Comparación entre sensores</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Año:</label>
            <input type="number" class="form-control" name="anio" value="<?= date('Y') ?>" required>
        </div>

        <div class="col-md-3">
            <label>Periodo:</label>
            <select class="form-control" name="periodo">
                <option value="dia">Diario</option>
                <option value="semana">Semanal</option>
                <option value="mes">Mensual</option>
                <option value="anual">Anual</option>
            </select>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100" id="botonComparar">Ver gráfica</button>
        </div>
    </form>

    <div>
        <canvas id="graficaCaudal" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('tipoGrafica').addEventListener('change', function () {
    const sensor2 = document.getElementById('sensor2Container');
    const boton = document.getElementById('botonComparar');
    if (this.value === 'comparacion_sensores') {
        sensor2.style.display = 'block';
        boton.textContent = 'Comparar sensores';
    } else {
        sensor2.style.display = 'none';
        boton.textContent = 'Ver gráfica';
    }
});

document.getElementById('formComparar').addEventListener('submit', function (e) {
    e.preventDefault();

    const tipoGrafica = document.getElementById('tipoGrafica').value;
    const sensor1 = document.getElementById('sensor1').value;
    const sensor2 = document.getElementById('sensor2')?.value || '';
    const periodo = document.querySelector('[name="periodo"]').value;
    const anio = document.querySelector('[name="anio"]').value;
    const datos = {
        sensor1,
        tipoGrafica,
        periodo,
        anio
    };

    if (tipoGrafica === 'comparacion_sensores') datos.sensor2 = sensor2;

    fetch('get_grafico_usuario.php', {
        method: 'POST',
        body: new URLSearchParams(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (!data || !data.labels || data.labels.length === 0) {
            alert("No se encontraron datos para los filtros seleccionados.");
            return;
        }
        const ctx = document.getElementById('graficaCaudal').getContext('2d');
        if (window.miGrafico) window.miGrafico.destroy();
        window.miGrafico = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets.map(ds => ({ ...ds, borderWidth: 1 }))
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: data.titulo },
                    legend: { display: true }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }).catch(error => {
        alert('Error al generar la gráfica: ' + error);
        console.error('Error gráfico:', error);
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>
