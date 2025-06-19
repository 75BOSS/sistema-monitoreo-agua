<?php
session_start();
include_once '../conexion.php';

// Asegurar que el usuario es técnico
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sensor_id'])) {
    $sensor_id = intval($_POST['sensor_id']);

    // Actualizar estado del sensor a 'en_reparacion'
    $query = $conexion->prepare("UPDATE sensores SET estado = 'en_reparacion' WHERE id = ?");
    $query->bind_param('i', $sensor_id);

    if ($query->execute()) {
        // Opcional: podrías agregar un registro en la tabla reparaciones si deseas
        // Redirigir de nuevo al dashboard
        header('Location: dashboard.php?estado=averiado');
        exit;
    } else {
        echo "Error al actualizar el estado.";
    }
} else {
    echo "Solicitud inválida.";
}
?>
