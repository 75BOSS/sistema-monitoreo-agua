<?php
// admin/usuarios.php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'header.php';

// Obtener lista de usuarios
$usuarios = $conexion->query("SELECT id, nombre, correo, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");

?>

<div class="container mt-5">
    <h2 class="mb-4">Gestión de Usuarios</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= ucfirst($u['rol']) ?></td>
                    <td><?= $u['fecha_registro'] ?></td>
                    <td>
                        <!-- Aquí podrías agregar botones de editar -->
                        <form method="POST" action="eliminar_usuario.php" onsubmit="return confirm('¿Eliminar este usuario?');" style="display:inline;">
                            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once 'footer.php'; ?>
