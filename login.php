<?php
session_start();
require_once 'includes/db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $clave = hash('sha256', $_POST['clave']);

    $stmt = $conn->prepare("SELECT id, nombre, rol FROM usuarios WHERE correo = ? AND contraseña = ?");
    $stmt->bind_param("ss", $correo, $clave);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nombre, $rol);
        $stmt->fetch();
        $_SESSION['id'] = $id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['rol'] = $rol;

        switch ($rol) {
            case 'administrador':
                header("Location: admin/dashboard.php");
                break;
            case 'tecnico':
                header("Location: tecnico/dashboard.php");
                break;
            case 'usuario':
                header("Location: usuario/dashboard.php");
                break;
            default:
                session_destroy();
                header("Location: login.php");
        }
        exit();
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <?php if ($mensaje) echo "<p style='color:red;'>$mensaje</p>"; ?>
    <form method="POST">
        <input type="email" name="correo" placeholder="Correo" required><br>
        <input type="password" name="clave" placeholder="Contraseña" required><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
