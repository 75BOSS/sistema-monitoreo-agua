<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $comunidad = trim($_POST['comunidad']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $estado = $_POST['estado'];

    $query = "INSERT INTO sensores (nombre, latitud, longitud, comunidad, ciudad, provincia, estado)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("sddssss", $nombre, $latitud, $longitud, $comunidad, $ciudad, $provincia, $estado);

    if ($stmt->execute()) {
        $mensaje = '<div class="alert alert-success">Sensor registrado correctamente.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al registrar el sensor.</div>';
    }

    $stmt->close();
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Registrar Nuevo Sensor</h2>

    <?= $mensaje ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre del Sensor</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Latitud</label>
            <input type="number" step="0.0000001" name="latitud" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Longitud</label>
            <input type="number" step="0.0000001" name="longitud" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Comunidad</label>
            <input type="text" name="comunidad" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="funcional">Funcional</option>
                <option value="averiado">Averiado</option>
                <option value="en_reparacion">En reparación</option>
            </select>
        </div>
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Sensor</button>
            <a href="sensores.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
