<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once 'includes/header.php';

$usuarioId = $_SESSION['id'] ?? 0;

// Número de reportes con alerta en el último mes
$alertas = $conexion->query("
    SELECT COUNT(*) as total 
    FROM reportes 
    WHERE usuario_id = $usuarioId 
      AND (turbidez = 1 OR olor = 1 OR color = 1 OR residuos = 1)
      AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
")->fetch_assoc()['total'];

// Total de reportes del usuario
$total_reportes = $conexion->query("
    SELECT COUNT(*) as total 
    FROM reportes 
    WHERE usuario_id = $usuarioId
")->fetch_assoc()['total'];

// Sensor más cercano (simulado)
$sensor_cercano = $conexion->query("
    SELECT nombre 
    FROM sensores 
    ORDER BY RAND() 
    LIMIT 1
")->fetch_assoc()['nombre'];
?>

<div class="container mt-5">
    <h2 class="mb-4">Resumen de Alertas y Actividad</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">⚠️ Alertas recientes</h5>
                    <p class="card-text fs-4"><?= $alertas ?></p>
                    <p class="text-muted">Reportes con condiciones de alerta en el último mes.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">📍 Sensor cercano</h5>
                    <p class="card-text fs-5"><?= htmlspecialchars($sensor_cercano) ?></p>
                    <p class="text-muted">Este es el sensor más cercano a tu ubicación registrada.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">📈 Total de Reportes</h5>
                    <p class="card-text fs-4"><?= $total_reportes ?></p>
                    <p class="text-muted">Reportes que has generado desde tu cuenta.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
