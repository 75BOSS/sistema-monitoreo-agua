<?php
include_once 'conexion.php';
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if ($nombre && $correo && $contrasena) {
        // Verificar si el correo ya está registrado
        $verifica = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $verifica->bind_param("s", $correo);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows > 0) {
            $error = 'Este correo ya está registrado.';
        } else {
            $query = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
            $query->bind_param("sss", $nombre, $correo, $contrasena);

            if ($query->execute()) {
                $exito = 'Registro exitoso. Ahora puedes iniciar sesión.';
            } else {
                $error = 'Error al registrar. Inténtalo nuevamente.';
            }
        }
    } else {
        $error = 'Todos los campos son obligatorios.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #e9f0f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .registro-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 430px;
        }
    </style>
</head>
<body>

<div class="registro-box">
    <h2 class="text-center mb-4">Registrarse</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="alert alert-success"><?= htmlspecialchars($exito) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
    </form>
</div>

</body>
</html>
