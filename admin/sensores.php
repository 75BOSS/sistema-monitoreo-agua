<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener sensores
$sensores = $conexion->query("SELECT * FROM sensores ORDER BY fecha_creacion DESC");
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Sensores Registrados</h2>
        <a href="nuevo_sensor.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Nuevo Sensor</a>
    </div>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Coordenadas</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($s = $sensores->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nombre']) ?></td>
                    <td><?= $s['latitud'] ?>, <?= $s['longitud'] ?></td>
                    <td><?= $s['comunidad'] ?>, <?= $s['ciudad'] ?>, <?= $s['provincia'] ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $s['estado'] === 'funcional' ? 'success' : 
                            ($s['estado'] === 'averiado' ? 'danger' : 'warning') ?>">
                            <?= ucfirst(str_replace('_', ' ', $s['estado'])) ?>
                        </span>
                    </td>
                    <td><?= $s['fecha_creacion'] ?></td>
                    <td>
                        <a href="editar_sensor.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="eliminar_sensor.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este sensor?');"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
