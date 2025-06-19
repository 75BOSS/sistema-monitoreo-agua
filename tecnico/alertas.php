<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header_tecnico.php';

$tecnico_id = $_SESSION['id'];

$query = $conexion->prepare("
    SELECT r.id, s.nombre AS sensor, s.comunidad, s.ciudad, s.provincia, r.fecha_inicio
    FROM reparaciones r
    JOIN sensores s ON r.sensor_id = s.id
    WHERE r.tecnico_id = ? AND r.estado = 'en_reparacion'
");
$query->bind_param('i', $tecnico_id);
$query->execute();
$resultado = $query->get_result();
?>

<div class="container mt-5">
    <h2 class="mb-4">Alertas y Reparaciones Activas</h2>

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
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['sensor']) ?></td>
                    <td><?= htmlspecialchars($row['comunidad'] . ', ' . $row['ciudad'] . ', ' . $row['provincia']) ?></td>
                    <td><?= $row['fecha_inicio'] ?></td>
                    <td>
                        <form method="POST" action="finalizar_reparacion.php">
                            <input type="hidden" name="reparacion_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Finalizar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No hay reparaciones activas actualmente.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
