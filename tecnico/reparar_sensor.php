<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sensor_id'])) {
    $sensor_id = intval($_POST['sensor_id']);
    $tecnico_id = $_SESSION['id'];
    $fecha_inicio = date('Y-m-d H:i:s');

    // Iniciar reparación (registrar en tabla y cambiar estado del sensor)
    $conexion->begin_transaction();

    try {
        // Insertar en tabla reparaciones
        $stmt1 = $conexion->prepare("
            INSERT INTO reparaciones (sensor_id, tecnico_id, fecha_inicio, estado)
            VALUES (?, ?, ?, 'en_reparacion')
        ");
        $stmt1->bind_param("iis", $sensor_id, $tecnico_id, $fecha_inicio);
        $stmt1->execute();

        // Actualizar estado del sensor
        $stmt2 = $conexion->prepare("
            UPDATE sensores SET estado = 'en_reparacion' WHERE id = ?
        ");
        $stmt2->bind_param("i", $sensor_id);
        $stmt2->execute();

        $conexion->commit();
        header('Location: dashboard.php?msg=reparacion_iniciada');
        exit;
    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error al iniciar reparación: " . $e->getMessage();
    }
} else {
    header('Location: dashboard.php');
    exit;
}
