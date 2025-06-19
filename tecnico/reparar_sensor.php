
<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sensor_id'])) {
    $sensor_id = $_POST['sensor_id'];

    $query = $conexion->prepare("UPDATE sensores SET estado = 'en_reparacion' WHERE id = ?");
    $query->bind_param('i', $sensor_id);
    $query->execute();

    header('Location: dashboard.php');
    exit;
}
?>
