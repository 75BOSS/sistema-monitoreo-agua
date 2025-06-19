<?php
session_start();
include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener sensores
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Visualización de Reportes</h2>

    <form id="formReportes" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Sensor:</label>
            <select class="form-control" id="sensor" name="sensor" required>
                <option value="">Seleccione</option>
                <?php while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Tipo de Gráfica:</label>
            <select class="form-control" id="tipoGrafica" name="tipoGrafica" required>
                <option value="historica">Histórica (caudal)</option>
                <option value="calidad">Calidad del agua</option>
                <option value="comparacion_fechas">Comparación por fechas</option>
                <option value="comparacion_temporadas">Temporada lluvia/sequía</option>
                <option value="anual">Anual</option>
            </select>
        </div>

        <div class="col-md-2">
            <label>Año:</label>
            <input type="number" class="form-control" name="anio" value="<?= date('Y') ?>">
        </div>

        <div class="col-md-2">
            <label>Periodo:</label>
            <select class="form-control" name="periodo">
                <option value="dia">Diario</option>
                <option value="semana">Semanal</option>
                <option value="mes">Mensual</option>
                <option value="anual">Anual</option>
            </select>
        </div>

        <div class="col-md-3 fecha-rango" style="display: none;">
            <label>Fecha inicio:</label>
            <input type="date" class="form-control" name="fecha_inicio">
        </div>

        <div class="col-md-3 fecha-rango" style="display: none;">
            <label>Fecha fin:</label>
            <input type="date" class="form-control" name="fecha_fin">
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100">Ver gráfica</button>
        </div>
    </form>

    <div>
        <canvas id="graficaReporte" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('tipoGrafica').addEventListener('change', function () {
    const fechaCampos = document.querySelectorAll('.fecha-rango');
    if (this.value === 'comparacion_fechas') {
        fechaCampos.forEach(el => el.style.display = 'block');
    } else {
        fechaCampos.forEach(el => el.style.display = 'none');
    }
});

document.getElementById('formReportes').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('get_grafico.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const ctx = document.getElementById('graficaReporte').getContext('2d');
        if (window.miGrafica) window.miGrafica.destroy();
        window.miGrafica = new Chart(ctx, {
            type: 'bar',
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
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>
