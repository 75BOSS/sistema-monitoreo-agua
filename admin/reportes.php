<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener lista de sensores para el selector
$sensores_result = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre");
$sensores = [];
while ($row = $sensores_result->fetch_assoc()) {
    $sensores[] = $row;
}

// Procesar formulario de comparación
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensor1 = $_POST['sensor1'];
    $sensor2 = $_POST['sensor2'];
    $anio = $_POST['anio'];

    $query = "SELECT sensor_id, MONTH(fecha) as mes, AVG(caudal_lps) as promedio
              FROM reportes
              WHERE tipo_reporte = 'caudal'
              AND sensor_id IN ($sensor1, $sensor2)
              AND YEAR(fecha) = $anio
              GROUP BY sensor_id, mes
              ORDER BY mes";
    $result = $conexion->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[$row['sensor_id']][$row['mes']] = round($row['promedio'], 2);
    }
}

$resultado = $conexion->query("SELECT r.id, r.tipo_reporte, r.fecha, r.hora, r.observaciones,
                                      s.nombre AS sensor, u.nombre AS usuario
                               FROM reportes r
                               JOIN sensores s ON r.sensor_id = s.id
                               JOIN usuarios u ON r.usuario_id = u.id
                               ORDER BY r.fecha DESC, r.hora DESC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Reportes del Sistema</h2>

    <form method="POST" class="row mb-4">
        <div class="col-md-3">
            <label for="sensor1">Sensor 1:</label>
            <select name="sensor1" class="form-control" required>
                <option value="">Seleccione</option>
                <?php foreach ($sensores as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="sensor2">Sensor 2:</label>
            <select name="sensor2" class="form-control" required>
                <option value="">Seleccione</option>
                <?php foreach ($sensores as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="anio">Año:</label>
            <input type="number" name="anio" class="form-control" min="2020" max="2025" required>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Comparar</button>
        </div>
    </form>

    <canvas id="caudalChart" height="100"></canvas>

    <hr class="my-5">
    <h3>Tabla de Reportes</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Sensor</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= ucfirst($r['tipo_reporte']) ?></td>
                    <td><?= $r['sensor'] ?></td>
                    <td><?= $r['usuario'] ?></td>
                    <td><?= $r['fecha'] ?></td>
                    <td><?= $r['hora'] ?></td>
                    <td><?= $r['observaciones'] ?: '---' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($data)): ?>
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const sensor1Data = [];
    const sensor2Data = [];

    for (let i = 1; i <= 12; i++) {
        sensor1Data.push(<?= json_encode($data[$_POST['sensor1']][i] ?? 0) ?>);
        sensor2Data.push(<?= json_encode($data[$_POST['sensor2']][i] ?? 0) ?>);
    }

    new Chart(document.getElementById('caudalChart'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Sensor <?= $_POST['sensor1'] ?>',
                    data: sensor1Data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                },
                {
                    label: 'Sensor <?= $_POST['sensor2'] ?>',
                    data: sensor2Data,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Comparación de Caudales por Mes'
                }
            }
        }
    });
<?php endif; ?>
</script>

<?php include_once '../includes/footer.php'; ?>
