<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

$query = $conexion->query("SELECT a.*, s.nombre AS sensor_nombre, s.comunidad, s.provincia 
                           FROM alertas a 
                           INNER JOIN sensores s ON a.sensor_id = s.id 
                           ORDER BY a.fecha DESC LIMIT 10");
?>

<div class='container mt-5'>
    <h2 class='mb-4'>Alertas Recientes</h2>
    <table class='table table-bordered'>
        <thead class='table-dark'>
            <tr>
                <th>Sensor</th>
                <th>Provincia</th>
                <th>Comunidad</th>
                <th>Fecha</th>
                <th>Tipo de Alerta</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $query->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['sensor_nombre']) ?></td>
                <td><?= htmlspecialchars($row['provincia']) ?></td>
                <td><?= htmlspecialchars($row['comunidad']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['tipo_alerta']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
