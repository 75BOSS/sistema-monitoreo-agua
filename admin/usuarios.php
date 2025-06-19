<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

// Verificar si el usuario actual es administrador
if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

// Manejar acciones: registrar, editar, eliminar, transferir rol
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["accion"])) {
        $accion = $_POST["accion"];

        if ($accion === "registrar") {
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $clave = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);
            $rol = $_POST["rol"];

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $correo, $clave, $rol);
            $stmt->execute();
            $mensaje = "Usuario registrado correctamente.";
        }

        if ($accion === "editar") {
            $id = $_POST["id"];
            $nombre = $_POST["nombre"];
            $correo = $_POST["correo"];
            $rol = $_POST["rol"];
            $clave = !empty($_POST["contraseña"]) ? password_hash($_POST["contraseña"], PASSWORD_DEFAULT) : null;

            if ($clave) {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, correo=?, contraseña=?, rol=? WHERE id=?");
                $stmt->bind_param("ssssi", $nombre, $correo, $clave, $rol, $id);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, correo=?, rol=? WHERE id=?");
                $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
            }
            $stmt->execute();
            $mensaje = "Usuario actualizado.";
        }

        if ($accion === "eliminar") {
            $id = $_POST["id"];
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $mensaje = "Usuario eliminado.";
        }

        if ($accion === "heredar") {
            $id_nuevo_admin = $_POST["id"];
            $stmt = $conn->prepare("UPDATE usuarios SET rol='administrador' WHERE id=?");
            $stmt->bind_param("i", $id_nuevo_admin);
            $stmt->execute();

            $id_actual_admin = $_SESSION["id"];
            $stmt = $conn->prepare("UPDATE usuarios SET rol='usuario' WHERE id=?");
            $stmt->bind_param("i", $id_actual_admin);
            $stmt->execute();

            $_SESSION["rol"] = "usuario"; // Cambiar rol actual en sesión
            header("Location: ../index.php");
            exit;
        }
    }
}

// Filtro por rol
$filtro = isset($_GET["rol"]) ? $_GET["rol"] : "";
$consulta = "SELECT * FROM usuarios";
if ($filtro && in_array($filtro, ["administrador", "tecnico", "usuario"])) {
    $consulta .= " WHERE rol='" . $conn->real_escape_string($filtro) . "'";
}
$usuarios = $conn->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Gestión de Usuarios</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <label for="rol">Filtrar por rol:</label>
        <select name="rol" onchange="this.form.submit()">
            <option value="">-- Todos --</option>
            <option value="administrador" <?= $filtro=="administrador"?"selected":"" ?>>Administrador</option>
            <option value="tecnico" <?= $filtro=="tecnico"?"selected":"" ?>>Técnico</option>
            <option value="usuario" <?= $filtro=="usuario"?"selected":"" ?>>Usuario</option>
        </select>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th><th>Correo</th><th>Rol</th><th>Fecha de registro</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td><input type="text" name="nombre" value="<?= $u['nombre'] ?>" class="form-control"></td>
                        <td><input type="email" name="correo" value="<?= $u['correo'] ?>" class="form-control"></td>
                        <td>
                            <select name="rol" class="form-control">
                                <option value="usuario" <?= $u["rol"]=="usuario"?"selected":"" ?>>Usuario</option>
                                <option value="tecnico" <?= $u["rol"]=="tecnico"?"selected":"" ?>>Técnico</option>
                                <option value="administrador" <?= $u["rol"]=="administrador"?"selected":"" ?>>Administrador</option>
                            </select>
                        </td>
                        <td><?= $u['fecha_registro'] ?></td>
                        <td>
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="password" name="contraseña" placeholder="Nueva clave" class="form-control mb-1">
                            <button name="accion" value="editar" class="btn btn-sm btn-primary">Editar</button>
                            <button name="accion" value="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</button>
                            <button name="accion" value="heredar" class="btn btn-sm btn-warning" <?= ($_SESSION['id'] == $u['id']) ? 'disabled' : '' ?>>Heredar admin</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>
    <h4>Registrar Nuevo Usuario</h4>
    <form method="POST" class="row g-3">
        <input type="hidden" name="accion" value="registrar">
        <div class="col-md-3">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="correo" class="form-control" placeholder="Correo" required>
        </div>
        <div class="col-md-3">
            <input type="password" name="contraseña" class="form-control" placeholder="Contraseña" required>
        </div>
        <div class="col-md-2">
            <select name="rol" class="form-control" required>
                <option value="usuario">Usuario</option>
                <option value="tecnico">Técnico</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-success">Registrar</button>
        </div>
    </form>
</body>
</html>