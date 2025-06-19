<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'conexion.php';

$mensaje = '';
$error = '';
$registro_exitoso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $error = "Ya existe un usuario con ese correo.";
    } else {
        $insertar = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
        $insertar->bind_param("sss", $nombre, $correo, $contrasena);

        if ($insertar->execute()) {
            $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
            $registro_exitoso = true;
        } else {
            $error = "Ocurrió un error al registrar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Monitoreo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            width: 100%;
        }
        .btn-login {
            width: 100%;
            margin-top: 10px;
            background-color: #28a745;
            border: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="mb-4 text-center">Registro de Usuario</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <a href="login.php" class="btn btn-login text-white">Iniciar sesión</a>
    <?php endif; ?>

    <?php if (!$registro_exitoso): ?>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" id="correo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarme</button>
        <p class="mt-3 text-center">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
