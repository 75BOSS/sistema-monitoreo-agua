<?php
// admin/usuarios.php - Gestión de usuarios
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener todos los usuarios
$resultado = $conexion->query("SELECT id, nombre, correo, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Gestión de Usuarios</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($usuario = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                <td><?= ucfirst($usuario['rol']) ?></td>
                <td><?= $usuario['fecha_registro'] ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <?php if ($_SESSION['id'] != $usuario['id']): ?>
                    <form method="POST" action="eliminar_usuario.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
