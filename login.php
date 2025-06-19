<?php
session_start();
include_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $query = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $query->bind_param("s", $correo);
    $query->execute();
    $resultado = $query->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Excepción: acceso directo para técnico
        if (
            $usuario['correo'] === '9876@papa.com' &&
            $contrasena === '9876' &&
            $usuario['rol'] === 'tecnico'
        ) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            header('Location: tecnico/dashboard.php');
            exit;
        }

        // Excepción: acceso directo para administrador
        if (
            $usuario['correo'] === 'adminoo1@correo.com' &&
            $contrasena === 'admin456' &&
            $usuario['rol'] === 'administrador'
        ) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            header('Location: admin/dashboard.php');
            exit;
        }

        // Verificación estándar (hash)
        if (password_verify($contrasena, $usuario['contraseña'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            switch ($usuario['rol']) {
                case 'administrador':
                    header('Location: admin/dashboard.php');
                    exit;
                case 'tecnico':
                    header('Location: tecnico/dashboard.php');
                    exit;
                case 'usuario':
                    header('Location: usuario/dashboard.php');
                    exit;
                default:
                    $error = 'Rol no reconocido.';
            }
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Correo no registrado';
    }
}
?>
