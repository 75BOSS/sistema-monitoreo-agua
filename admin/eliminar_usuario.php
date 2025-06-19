<?php
// eliminar_usuario.php - Lógica para eliminar un usuario desde el panel de administrador
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Verificar que no se elimine el usuario administrador actual
    if ($id === $_SESSION['id']) {
        $_SESSION['mensaje_error'] = 'No puedes eliminar tu propio usuario.';
        header('Location: usuarios.php');
        exit;
    }

    // Ejecutar la eliminación
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = 'Usuario eliminado correctamente.';
    } else {
        $_SESSION['mensaje_error'] = 'Error al eliminar el usuario.';
    }

    $stmt->close();
}

header('Location: usuarios.php');
exit;
?>
