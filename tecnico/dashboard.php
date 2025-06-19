<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include '../conexion.php';

// Conteos rápidos
$total_sensores = 0;
$sensores_funcionales = 0;
$sensores_averiados = 0;
$sensores_en_reparacion = 0;
$ultimos_reportes = [];
$ultimas_reparaciones = [];

// Recuento de sensores por estado
$query_sensores_status = "SELECT estado, COUNT(*) as count FROM sensores GROUP BY estado";
$result_sensores_status = $conexion->query($query_sensores_status);
if ($result_sensores_status) {
    while ($row = $result_sensores_status->fetch_assoc()) {
        $total_sensores += $row['count'];
        switch ($row['estado']) {
            case 'funcional': $sensores_funcionales = $row['count']; break;
            case 'averiado': $sensores_averiados = $row['count']; break;
            case 'en_reparacion': $sensores_en_reparacion = $row['count']; break;
        }
    }
}

// Últimos 5 reportes
$query_ultimos_reportes = "SELECT r.tipo_reporte, s.nombre as sensor_nombre, r.fecha, r.hora, r.observaciones
                           FROM reportes r
                           JOIN sensores s ON r.sensor_id = s.id
                           ORDER BY r.fecha DESC, r.hora DESC
                           LIMIT 5";
$result_ultimos_reportes = $conexion->query($query_ultimos_reportes);
if ($result_ultimos_reportes && $result_ultimos_reportes->num_rows > 0) {
    while ($row = $result_ultimos_reportes->fetch_assoc()) {
        $ultimos_reportes[] = $row;
    }
}

// Últimas 5 reparaciones
$query_ultimas_reparaciones = "SELECT rep.fecha_inicio, rep.fecha_fin, s.nombre as sensor_nombre, u.nombre as tecnico_nombre, rep.estado
                                FROM reparaciones rep
                                JOIN sensores s ON rep.sensor_id = s.id
                                JOIN usuarios u ON rep.tecnico_id = u.id
                                ORDER BY rep.fecha_inicio DESC
                                LIMIT 5";
$result_ultimas_reparaciones = $conexion->query($query_ultimas_reparaciones);
if ($result_ultimas_reparaciones && $result_ultimas_reparaciones->num_rows > 0) {
    while ($row = $result_ultimas_reparaciones->fetch_assoc()) {
        $ultimas_reparaciones[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h1 class="mb-4">Bienvenido, Técnico <?= htmlspecialchars($_SESSION['nombre']) ?></h1>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Funcionales</h5>
                    <p class="card-text fs-4"><?= $sensores_funcionales ?> / <?= $total_sensores ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Averiados</h5>
                    <p class="card-text fs-4"><?= $sensores_averiados ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">En reparación</h5>
                    <p class="card-text fs-4"><?= $sensores_en_reparacion ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Total Sensores</h5>
                    <p class="card-text fs-4"><?= $total_sensores ?></p>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-6">
            <h3>Últimos Reportes</h3>
            <?php if (!empty($ultimos_reportes)): ?>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Sensor</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_reportes as $reporte): ?>
                            <tr>
                                <td><?= ucfirst($reporte['tipo_reporte']) ?></td>
                                <td><?= $reporte['sensor_nombre'] ?></td>
                                <td><?= $reporte['fecha'] ?></td>
                                <td><?= $reporte['hora'] ?></td>
                                <td><?= $reporte['observaciones'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay reportes recientes.</p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h3>Últimas Reparaciones</h3>
            <?php if (!empty($ultimas_reparaciones)): ?>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Sensor</th>
                            <th>Técnico</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_reparaciones as $rep): ?>
                            <tr>
                                <td><?= $rep['sensor_nombre'] ?></td>
                                <td><?= $rep['tecnico_nombre'] ?></td>
                                <td><?= $rep['fecha_inicio'] ?></td>
                                <td><?= $rep['fecha_fin'] ?? 'N/A' ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $rep['estado'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay reparaciones recientes.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="../logout.php" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
</div>

</body>
</html>
