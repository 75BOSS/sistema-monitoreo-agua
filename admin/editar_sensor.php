<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

$mensaje = '';
$id = $_GET['id'] ?? null;

// Validar ID del sensor
if (!$id || !is_numeric($id)) {
    header('Location: sensores.php');
    exit;
}

// Obtener datos actuales del sensor
$stmt = $conexion->prepare("SELECT * FROM sensores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: sensores.php');
    exit;
}

$sensor = $result->fetch_assoc();
$stmt->close();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $comunidad = trim($_POST['comunidad']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $estado = $_POST['estado'];

    $update = $conexion->prepare("UPDATE sensores SET nombre = ?, latitud = ?, longitud = ?, comunidad = ?, ciudad = ?, provincia = ?, estado = ? WHERE id = ?");
    $update->bind_param("sddssssi", $nombre, $latitud, $longitud, $comunidad, $ciudad, $provincia, $estado, $id);

    if ($update->execute()) {
        $mensaje = '<div class="alert alert-success">Sensor actualizado correctamente.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar el sensor.</div>';
    }

    $update->close();
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Editar Sensor</h2>
    <?= $mensaje ?>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($sensor['nombre']) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Latitud</label>
            <input type="number" step="0.0000001" name="latitud" class="form-control" value="<?= $sensor['latitud'] ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Longitud</label>
            <input type="number" step="0.0000001" name="longitud" class="form-control" value="<?= $sensor['longitud'] ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Comunidad</label>
            <input type="text" name="comunidad" class="form-control" value="<?= htmlspecialchars($sensor['comunidad']) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($sensor['ciudad']) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Provincia</label>
            <input type="text" name="provincia" class="form-control" value="<?= htmlspecialchars($sensor['provincia']) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="funcional" <?= $sensor['estado'] === 'funcional' ? 'selected' : '' ?>>Funcional</option>
                <option value="averiado" <?= $sensor['estado'] === 'averiado' ? 'selected' : '' ?>>Averiado</option>
                <option value="en_reparacion" <?= $sensor['estado'] === 'en_reparacion' ? 'selected' : '' ?>>En reparación</option>
            </select>
        </div>
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
            <a href="sensores.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
