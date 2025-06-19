<?php
session_start();
include_once 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    $query = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $query->bind_param("s", $correo);
    $query->execute();
    $resultado = $query->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Si estás usando contraseñas sin hash:
        if ($contrasena === $usuario['contraseña']) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            switch ($usuario['rol']) {
                case 'administrador': header('Location: admin/dashboard.php'); break;
                case 'tecnico': header('Location: tecnico/dashboard.php'); break;
                case 'usuario': header('Location: usuario/dashboard.php'); break;
            }
            exit;
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Correo no registrado';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2 class="text-center mb-4">Iniciar Sesión</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <div class="mt-3 text-center">
            ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
        </div>
    </form>
</div>

</body>
</html>
