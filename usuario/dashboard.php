<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';

// Obtener sensores disponibles
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Usuario</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reportes</a></li>
            </ul>
            <span class="navbar-text text-white me-3">
                <?php echo $_SESSION['nombre']; ?>
            </span>
            <a class="btn btn-outline-light" href="../logout.php">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3>Bienvenido, <?php echo $_SESSION['nombre']; ?></h3>

    <form id="formGraficas" class="row g-3 align-items-end mt-3">
        <div class="col-md-4">
            <label for="sensor" class="form-label">Sensor:</label>
            <select id="sensor" name="sensor1" class="form-select" required>
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
                <option value="comparacion_temporadas">Temporadas</option>
                <option value="calidad">Calidad del agua</option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="anio" class="form-label">Año:</label>
            <input type="number" id="anio" name="anio" class="form-control" value="<?= date('Y') ?>">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Generar Gráfica</button>
        </div>
    </form>

    <div class="row mt-3" id="fechasComparacion" style="display:none;">
        <div class="col-md-3">
            <label for="fecha_inicio" class="form-label">Fecha inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="fecha_fin" class="form-label">Fecha fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
        </div>
    </div>

    <div class="mt-4">
        <canvas id="grafico" height="100"></canvas>
    </div>
</div>

<script>
const form = document.getElementById('formGraficas');
const tipoGrafica = document.getElementById('tipoGrafica');
const fechasComparacion = document.getElementById('fechasComparacion');
const ctx = document.getElementById('grafico').getContext('2d');
let chart = null;

tipoGrafica.addEventListener('change', function () {
    fechasComparacion.style.display = this.value === 'comparacion_fechas' ? 'flex' : 'none';
});

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(form);

    if (tipoGrafica.value === 'comparacion_fechas') {
        formData.append('fecha_inicio', document.getElementById('fecha_inicio').value);
        formData.append('fecha_fin', document.getElementById('fecha_fin').value);
    }

    
    fetch('get_grafico_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error("Error en la respuesta del servidor");
        return res.json();
    })
    .then(data => {
        if (!data || !data.labels || data.labels.length === 0) {
            alert("No se encontraron datos para los filtros seleccionados.");
            return;
        }
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
                    title: {
                        display: true,
                        text: data.titulo || 'Gráfica'
                    }
                }
            }
        });
    })
    .catch(err => {
        console.error(err);
        alert('Error al generar la gráfica');
    });
});
</script>
</body>
</html>
