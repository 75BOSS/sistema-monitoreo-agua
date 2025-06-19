<?php
// admin/dashboard.php - Panel principal del administrador
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../includes/db.php';
include_once '../includes/header.php';

// Conteos rápidos
$total_usuarios = mysqli_fetch_assoc($conexion->query("SELECT COUNT(*) as total FROM usuarios"))['total'];
$total_sensores = mysqli_fetch_assoc($conexion->query("SELECT COUNT(*) as total FROM sensores"))['total'];
$total_reportes = mysqli_fetch_assoc($conexion->query("SELECT COUNT(*) as total FROM reportes"))['total'];
$total_reparaciones = mysqli_fetch_assoc($conexion->query("SELECT COUNT(*) as total FROM reparaciones"))['total'];

// Obtener los últimos reportes
$reportes_recientes = $conexion->query("SELECT r.tipo_reporte, r.fecha, r.hora, u.nombre AS usuario, s.nombre AS sensor
                                        FROM reportes r
                                        JOIN usuarios u ON r.usuario_id = u.id
                                        JOIN sensores s ON r.sensor_id = s.id
                                        ORDER BY r.fecha DESC, r.hora DESC LIMIT 5");

// Obtener los sensores más activos (con más reportes)
$sensores_activos = $conexion->query("SELECT s.nombre, COUNT(r.id) as total_reportes
                                      FROM sensores s
                                      JOIN reportes r ON r.sensor_id = s.id
                                      GROUP BY s.id
                                      ORDER BY total_reportes DESC
                                      LIMIT 5");
?>

<div class="container mt-5">
    <h1 class="mb-4">Panel del Administrador</h1>

    <div class="row text-center">
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Usuarios</div>
                <div class="card-footer fs-4"> <?= $total_usuarios ?> </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Sensores</div>
                <div class="card-footer fs-4"> <?= $total_sensores ?> </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark mb-4">
                <div class="card-body">Reportes</div>
                <div class="card-footer fs-4"> <?= $total_reportes ?> </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Reparaciones</div>
                <div class="card-footer fs-4"> <?= $total_reparaciones ?> </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>Reportes recientes</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Tipo</th><th>Sensor</th><th>Usuario</th><th>Fecha</th><th>Hora</th></tr>
                </thead>
                <tbody>
                    <?php while ($r = $reportes_recientes->fetch_assoc()): ?>
                    <tr>
                        <td><?= ucfirst($r['tipo_reporte']) ?></td>
                        <td><?= $r['sensor'] ?></td>
                        <td><?= $r['usuario'] ?></td>
                        <td><?= $r['fecha'] ?></td>
                        <td><?= $r['hora'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4>Sensores más activos</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Sensor</th><th>Total de Reportes</th></tr>
                </thead>
                <tbody>
                    <?php while ($s = $sensores_activos->fetch_assoc()): ?>
                    <tr>
                        <td><?= $s['nombre'] ?></td>
                        <td><?= $s['total_reportes'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
