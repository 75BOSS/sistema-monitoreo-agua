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
        if (password_verify($contrasena, $usuario['contraseña'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según rol
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
    <title>Iniciar Sesión - Monitoreo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f4f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="text-center">Iniciar Sesión</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" id="correo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>

</body>
</html>
