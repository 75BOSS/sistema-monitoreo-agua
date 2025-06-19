<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header_usuario.php'; // Asegúrate de que este archivo existe

$usuario_id = $_SESSION['id'];

// Buscar reportes del usuario con condiciones de alerta
$query = $conexion->prepare("
    SELECT sensores.nombre AS sensor, caudal_lps, turbidez, olor, color, residuos, fecha, hora
    FROM reportes
    JOIN sensores ON reportes.sensor_id = sensores.id
    WHERE usuario_id = ?
    ORDER BY fecha DESC, hora DESC
    LIMIT 10
");
$query->bind_param('i', $usuario_id);
$query->execute();
$resultado = $query->get_result();
?>

<div class="container mt-5">
    <h2 class="mb-4">📢 Alertas Recientes</h2>

    <?php if ($resultado->num_rows === 0): ?>
        <div class="alert alert-info">No se han generado alertas recientemente.</div>
    <?php else: ?>
        <?php while ($row = $resultado->fetch_assoc()): ?>
            <?php
            $problemas = [];

            if ($row['caudal_lps'] < 1) {
                $problemas[] = "Caudal bajo ({$row['caudal_lps']} L/s)";
            }

            if ($row['turbidez']) $problemas[] = "Turbidez detectada";
            if ($row['olor']) $problemas[] = "Olor inusual";
            if ($row['color']) $problemas[] = "Color anormal";
            if ($row['residuos']) $problemas[] = "Presencia de residuos";

            if (count($problemas) > 0): ?>
                <div class="alert alert-warning">
                    <strong>Sensor:</strong> <?= htmlspecialchars($row['sensor']) ?><br>
                    <strong>Fecha:</strong> <?= $row['fecha'] ?> <?= $row['hora'] ?><br>
                    <strong>Problemas detectados:</strong>
                    <ul>
                        <?php foreach ($problemas as $p): ?>
                            <li><?= htmlspecialchars($p) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
