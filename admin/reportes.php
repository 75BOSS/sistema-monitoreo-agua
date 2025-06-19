<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener sensores para los select
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Reportes del Sistema</h2>

    <form method="GET" id="compararForm" class="row g-3">
        <div class="col-md-3">
            <label>Sensor 1:</label>
            <select class="form-control" name="sensor1">
                <option value="">Seleccione</option>
                <?php foreach ($sensores as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Sensor 2:</label>
            <select class="form-control" name="sensor2">
                <option value="">Seleccione</option>
                <?php foreach ($sensores as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Año:</label>
            <input class="form-control" type="number" name="year" value="<?= date('Y') ?>">
        </div>
        <div class="col-md-2">
            <label>Ver por:</label>
            <select name="modo" class="form-control">
                <option value="mensual">Mensual</option>
                <option value="semanal">Semanal</option>
                <option value="diario">Diario</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Comparar</button>
        </div>
    </form>

    <canvas id="graficoCaudal" height="100" class="mt-5"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('compararForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const params = new URLSearchParams(formData);

    fetch('get_grafico.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('graficoCaudal').getContext('2d');
            if (window.chartInstance) window.chartInstance.destroy();
            window.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: data.nombre1,
                            data: data.valores1,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            tension: 0.3
                        },
                        {
                            label: data.nombre2,
                            data: data.valores2,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
});
</script>

<?php include_once '../includes/footer.php'; ?>
