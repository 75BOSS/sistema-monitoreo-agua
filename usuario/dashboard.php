<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Bienvenido, <?= $_SESSION['nombre'] ?></h2>

    <form id="formGraficas" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Sensor:</label>
            <select class="form-control" name="sensor1" id="sensor1" required>
                <option value="">Seleccione</option>
                <?php while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Tipo de Gráfica:</label>
            <select class="form-control" name="tipoGrafica" id="tipoGrafica">
                <option value="historica">Histórica de caudal</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>Año:</label>
            <input type="number" class="form-control" name="anio" value="<?= date('Y') ?>">
        </div>

        <div class="col-md-2">
            <label>Periodo:</label>
            <select class="form-control" name="periodo">
                <option value="dia">Día</option>
                <option value="semana">Semana</option>
                <option value="mes" selected>Mes</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Generar Gráfica</button>
        </div>
    </form>

    <div>
        <canvas id="graficaCaudal" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('formGraficas').addEventListener('submit', function(e) {
    e.preventDefault();

    const datos = new FormData(this);

    fetch('../get_grafico_usuario.php', {
        method: 'POST',
        body: datos
    })
    .then(res => res.json())
    .then(data => {
        if (!data || !data.labels.length) {
            alert('No se encontraron datos.');
            return;
        }

        const ctx = document.getElementById('graficaCaudal').getContext('2d');
        if (window.miGrafico) window.miGrafico.destroy();

        window.miGrafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: data.titulo
                    }
                }
            }
        });
    })
    .catch(err => alert('Error al generar la gráfica.'));
});
</script>

<?php include_once '../includes/footer.php'; ?>
