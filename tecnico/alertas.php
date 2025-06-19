<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header_tecnico.php';

// Obtener reparaciones activas
$query = $conexion->query("
    SELECT r.id, s.nombre AS sensor, s.comunidad, s.ciudad, s.provincia, r.fecha_inicio 
    FROM reparaciones r
    JOIN sensores s ON r.sensor_id = s.id
    WHERE r.estado = 'en_reparacion' AND r.tecnico_id = " . intval($_SESSION['id']) . "
");
?>

<div class="container mt-5">
    <h2 class="mb-4">Alertas y Reparaciones Activas</h2>

    <?php if ($query->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Sensor</th>
                    <th>Ubicación</th>
                    <th>Fecha de Inicio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['sensor'] ?></td>
                    <td><?= $row['comunidad'] ?>, <?= $row['ciudad'] ?>, <?= $row['provincia'] ?></td>
                    <td><?= $row['fecha_inicio'] ?></td>
                    <td>
                        <form method="POST" action="finalizar_reparacion.php">
                            <input type="hidden" name="reparacion_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Finalizar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No tienes reparaciones activas.</div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
