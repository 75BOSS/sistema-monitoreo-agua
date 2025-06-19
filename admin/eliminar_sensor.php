<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';

// Verificar que se recibió un ID válido
$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    // Preparar y ejecutar eliminación
    $stmt = $conexion->prepare("DELETE FROM sensores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirigir de vuelta a la lista de sensores
header('Location: sensores.php');
exit;
