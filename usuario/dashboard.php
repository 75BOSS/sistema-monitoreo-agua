<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener sensores disponibles
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?> </h2>

    <div class="card p-4 mb-4">
        <form id="formGraficas">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="sensor1" class="form-label">Sensor:</label>
                    <select id="sensor1" name="sensor1" class="form-select" required>
                        <option value="">Seleccione</option>
                        <?php while ($s = $sensores->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tipoGrafica" class="form-label">Tipo de Gráfica:</label>
                    <select id="tipoGrafica" name="tipoGrafica" class="form-select">
                        <option value="historica">Histórica de caudal</option>
                        <option value="comparacion_fechas">Comparación por fechas</option>
                        <option value="comparacion_temporadas">Temporada lluvia vs sequía</option>
                        <option value="calidad">Parámetros de calidad</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="anio" class="form-label">Año:</label>
                    <input type="number" id="anio" name="anio" class="form-control" value="<?= date('Y') ?>">
                </div>
            </div>
            <div class="row mb-3" id="filtrosFechas" style="display: none;">
                <div class="col-md-6">
                    <label for="fecha_inicio" class="form-label">Fecha inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="fecha_fin" class="form-label">Fecha fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Generar Gráfica</button>
        </form>
    </div>

    <div class="card p-3">
        <h5 id="tituloGrafica"></h5>
        <canvas id="grafico" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('tipoGrafica').addEventListener('change', function () {
    const mostrar = this.value === 'comparacion_fechas';
    document.getElementById('filtrosFechas').style.display = mostrar ? 'flex' : 'none';
});

const ctx = document.getElementById('grafico').getContext('2d');
let chart;

function renderGrafico(data) {
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: data.datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: data.titulo }
            }
        }
    });
}

document.getElementById('formGraficas').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../admin/get_graficos.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('tituloGrafica').textContent = data.titulo;
        renderGrafico(data);
    })
    .catch(err => alert('Error al generar la gráfica'));
});
</script>

<?php include_once '../includes/footer.php'; ?>
