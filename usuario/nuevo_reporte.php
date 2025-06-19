<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header_usuario.php';


// Obtener sensores para el formulario
$sensores = $conexion->query("SELECT id, nombre FROM sensores WHERE estado = 'funcional'");
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensor_id = $_POST['sensor_id'];
    $tipo_reporte = $_POST['tipo_reporte'];
    $usuario_id = $_SESSION['id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    if ($tipo_reporte === 'caudal') {
        $caudal = $_POST['caudal'];
        $stmt = $conexion->prepare("INSERT INTO reportes (sensor_id, usuario_id, tipo_reporte, caudal_lps, fecha, hora, latitud, longitud) VALUES (?, ?, 'caudal', ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidssdd", $sensor_id, $usuario_id, $caudal, $fecha, $hora, $latitud, $longitud);
    } else {
        $turbidez = isset($_POST['turbidez']) ? 1 : 0;
        $olor = isset($_POST['olor']) ? 1 : 0;
        $color = isset($_POST['color']) ? 1 : 0;
        $residuos = isset($_POST['residuos']) ? 1 : 0;
        $observaciones = $_POST['observaciones'];
        $stmt = $conexion->prepare("INSERT INTO reportes (sensor_id, usuario_id, tipo_reporte, turbidez, olor, color, residuos, observaciones, fecha, hora, latitud, longitud) VALUES (?, ?, 'calidad', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiiisssdd", $sensor_id, $usuario_id, $turbidez, $olor, $color, $residuos, $observaciones, $fecha, $hora, $latitud, $longitud);
    }

    if ($stmt->execute()) {
        $mensaje = "✅ Reporte guardado correctamente.";
    } else {
        $mensaje = "❌ Error al guardar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Reporte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function cambiarFormulario() {
            const tipo = document.getElementById('tipo_reporte').value;
            document.getElementById('form_caudal').style.display = tipo === 'caudal' ? 'block' : 'none';
            document.getElementById('form_calidad').style.display = tipo === 'calidad' ? 'block' : 'none';
        }

        window.onload = function () {
            cambiarFormulario();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('latitud').value = position.coords.latitude;
                    document.getElementById('longitud').value = position.coords.longitude;
                }, function () {
                    alert("No se pudo obtener la ubicación.");
                });
            } else {
                alert("Geolocalización no disponible.");
            }
        };
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Generar Nuevo Reporte</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info mt-3"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="sensor_id">Sensor:</label>
            <select name="sensor_id" class="form-control" required>
                <option value="">Seleccione un sensor</option>
                <?php while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="tipo_reporte">Tipo de Reporte:</label>
            <select id="tipo_reporte" name="tipo_reporte" class="form-control" onchange="cambiarFormulario()" required>
                <option value="caudal">Caudal</option>
                <option value="calidad">Calidad</option>
            </select>
        </div>

        <!-- Formulario para caudal -->
        <div id="form_caudal">
            <div class="mb-3">
                <label for="caudal">Caudal (L/s):</label>
                <input type="number" name="caudal" step="0.01" class="form-control">
            </div>
        </div>

        <!-- Formulario para calidad -->
        <div id="form_calidad" style="display: none;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="turbidez" id="turbidez">
                <label class="form-check-label" for="turbidez">Turbidez</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="olor" id="olor">
                <label class="form-check-label" for="olor">Olor</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="color" id="color">
                <label class="form-check-label" for="color">Color</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="residuos" id="residuos">
                <label class="form-check-label" for="residuos">Residuos</label>
            </div>
            <div class="mb-3">
                <label for="observaciones">Observaciones:</label>
                <textarea name="observaciones" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <!-- Campos comunes -->
        <div class="mb-3">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="hora">Hora:</label>
            <input type="time" name="hora" class="form-control" required>
        </div>
        <input type="hidden" id="latitud" name="latitud">
        <input type="hidden" id="longitud" name="longitud">

        <button type="submit" class="btn btn-success">Guardar Reporte</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
