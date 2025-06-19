<?php
// admin/editar_usuario.php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

require_once '../conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos actuales del usuario
$stmt = $conexion->prepare("SELECT nombre, correo, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: usuarios.php?error=usuario_no_encontrado');
    exit;
}

$usuario = $result->fetch_assoc();
$stmt->close();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];

    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?exito=usuario_actualizado");
    } else {
        $error = "Error al actualizar usuario.";
    }

    $stmt->close();
}
?>

<?php include_once '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Editar Usuario</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
                <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="tecnico" <?= $usuario['rol'] === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
