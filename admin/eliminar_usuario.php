<?php
// admin/eliminar_usuario.php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

require_once '../conexion.php';

// Verifica si se recibió un ID válido por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Evitar que un administrador se elimine a sí mismo
    if ($_SESSION['id'] == $id) {
        header('Location: usuarios.php?error=no_autodestruccion');
        exit;
    }

    // Eliminar el usuario
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: usuarios.php?exito=usuario_eliminado');
    } else {
        header('Location: usuarios.php?error=fallo_eliminacion');
    }

    $stmt->close();
} else {
    header('Location: usuarios.php?error=sin_datos');
}
exit;
