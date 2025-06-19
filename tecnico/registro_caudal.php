<?php
session_start();
require_once '../conexion.php'; // ✅ Ruta a la conexión correcta

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'tecnico') {
    header("Location: ../login.php");
    exit;
}

function escape_data($data) {
    return htmlspecialchars(trim($data));
}

$message = '';

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sensor_id = escape_data($_POST['sensor_id']);
    $fecha = escape_data($_POST['fecha']);
    $hora = escape_data($_POST['hora']);
    $caudal_lps = escape_data($_POST['caudal_lps']);
    $latitud = escape_data($_POST['latitud']);
    $longitud = escape_data($_POST['longitud']);
    $observaciones = escape_data($_POST['observaciones']);
    $usuario_id = $_SESSION['id'];

    $sql_insert = "INSERT INTO reportes (sensor_id, usuario_id, tipo_reporte, caudal_lps, fecha, hora, latitud, longitud, observaciones)
                   VALUES ('$sensor_id', '$usuario_id', 'caudal', '$caudal_lps', '$fecha', '$hora', '$latitud', '$longitud', '$observaciones')";

    if ($conexion->query($sql_insert) === TRUE) {
        $message = "<div class='alert alert-success'>✅ Reporte de caudal registrado con éxito.</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: {$conexion->error}</div>";
    }
}

// Obtener sensores
$sensores = [];
$sql_sensores = "SELECT id, nombre FROM sensores ORDER BY nombre ASC";
$result = $conexion->query($sql_sensores);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sensores[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Caudal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-tint"></i> Registro de Caudal</h4>
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Sensor asociado</label>
                    <select class="form-select" name="sensor_id" required>
                        <option value="">Selecciona un sensor</option>
                        <?php foreach ($sensores as $sensor): ?>
                            <option value="<?= $sensor['id'] ?>"><?= $sensor['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora</label>
                        <input type="time" name="hora" class="form-control" value="<?= date('H:i') ?>" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Caudal (LPS)</label>
                    <input type="number" step="0.01" name="caudal_lps" class="form-control" placeholder="Ej: 15.25" required>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Latitud</label>
                        <input type="number" step="0.0000001" name="latitud" class="form-control" placeholder="-0.22985" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitud</label>
                        <input type="number" step="0.0000001" name="longitud" class="form-control" placeholder="-78.52495" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas..."></textarea>
                </div>
                <div class="text-center mt-4">
                    <button class="btn btn-success px-5">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://kit.fontawesome.com/ab9b5f3b2b.js" crossorigin="anonymous"></script>
</body>
</html>
