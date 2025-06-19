<?php
// dashboard.php - Panel principal para el rol de tC)cnico

// Incluye el encabezado que contiene la verificaciC3n de autenticaciC3n
include_once 'header.php';
require_once '../config.php'; // AsegC:rate de que la ruta sea correcta

// --- LC3gica para obtener un resumen del estado del sistema ---
$total_sensores = 0;
$sensores_funcionales = 0;
$sensores_averiados = 0;
$sensores_en_reparacion = 0;
$ultimos_reportes = [];
$ultimas_reparaciones = [];

// Obtener recuento de sensores por estado
$query_sensores_status = "SELECT estado, COUNT(*) as count FROM sensores GROUP BY estado";
$result_sensores_status = $conn->query($query_sensores_status);

if ($result_sensores_status) {
    while ($row = $result_sensores_status->fetch_assoc()) {
        $total_sensores += $row['count'];
        switch ($row['estado']) {
            case 'funcional':
                $sensores_funcionales = $row['count'];
                break;
            case 'averiado':
                $sensores_averiados = $row['count'];
                break;
            case 'en_reparacion':
                $sensores_en_reparacion = $row['count'];
                break;
        }
    }
}

// Obtener los C:ltimos 5 reportes (sin importar tipo)
$query_ultimos_reportes = "SELECT r.tipo_reporte, s.nombre as sensor_nombre, r.fecha, r.hora, r.observaciones
                           FROM reportes r
                           JOIN sensores s ON r.sensor_id = s.id
                           ORDER BY r.fecha DESC, r.hora DESC
                           LIMIT 5";
$result_ultimos_reportes = $conn->query($query_ultimos_reportes);

if ($result_ultimos_reportes && $result_ultimos_reportes->num_rows > 0) {
    while ($row = $result_ultimos_reportes->fetch_assoc()) {
        $ultimos_reportes[] = $row;
    }
}

// Obtener las C:ltimas 5 reparaciones iniciadas/finalizadas
$query_ultimas_reparaciones = "SELECT rep.fecha_inicio, rep.fecha_fin, s.nombre as sensor_nombre, u.nombre as tecnico_nombre, rep.estado
                                FROM reparaciones rep
                                JOIN sensores s ON rep.sensor_id = s.id
                                JOIN usuarios u ON rep.tecnico_id = u.id
                                ORDER BY rep.fecha_inicio DESC
                                LIMIT 5";
$result_ultimas_reparaciones = $conn->query($query_ultimas_reparaciones);

if ($result_ultimas_reparaciones && $result_ultimas_reparaciones->num_rows > 0) {
    while ($row = $result_ultimas_reparaciones->fetch_assoc()) {
        $ultimas_reparaciones[] = $row;
    }
}

// Cerrar la conexiC3n a la base de datos
$conn->close();
?>

<div class="content-section">
    <h2><i class="fas fa-chart-line"></i> Resumen del Sistema</h2>
=======
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include '../conexion.php';

// Conteos rápidos
$sensores = mysqli_query($conexion, "SELECT COUNT(*) as total FROM sensores");
$averiados = mysqli_query($conexion, "SELECT COUNT(*) as total FROM sensores WHERE estado = 'averiado'");
$reparacion = mysqli_query($conexion, "SELECT COUNT(*) as total FROM reparaciones WHERE estado = 'en_reparacion'");
$alertas = mysqli_query($conexion, "SELECT COUNT(*) as total FROM reportes WHERE (caudal_lps > 100 OR turbidez = 1 OR olor = 1 OR color = 1 OR residuos = 1)");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Bienvenido, Técnico <?= $_SESSION['nombre'] ?></h1>
>>>>>>> af0a95a075714694cfb09f13628ce3b11463827f

    <div class="dashboard-summary">
        <div class="summary-card functional">
            <h3>Sensores Funcionales</h3>
            <p><?php echo $sensores_funcionales; ?> / <?php echo $total_sensores; ?></p>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="summary-card damaged">
            <h3>Sensores Averiados</h3>
            <p><?php echo $sensores_averiados; ?> / <?php echo $total_sensores; ?></p>
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="summary-card repairing">
            <h3>Sensores en ReparaciC3n</h3>
            <p><?php echo $sensores_en_reparacion; ?> / <?php echo $total_sensores; ?></p>
            <i class="fas fa-tools"></i>
        </div>
        <div class="summary-card total">
            <h3>Total Sensores</h3>
            <p><?php echo $total_sensores; ?></p>
            <i class="fas fa-microchip"></i>
        </div>
    </div>

    <div class="recent-activities">
        <div class="activity-card">
            <h3>C:ltimos Reportes</h3>
            <?php if (!empty($ultimos_reportes)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Sensor</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>ObservaciC3n</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimos_reportes as $reporte): ?>
                            <tr>
                                <td data-label="Tipo"><?php echo htmlspecialchars(ucfirst($reporte['tipo_reporte'])); ?></td>
                                <td data-label="Sensor"><?php echo htmlspecialchars($reporte['sensor_nombre']); ?></td>
                                <td data-label="Fecha"><?php echo htmlspecialchars($reporte['fecha']); ?></td>
                                <td data-label="Hora"><?php echo htmlspecialchars($reporte['hora']); ?></td>
                                <td data-label="ObservaciC3n"><?php echo htmlspecialchars($reporte['observaciones']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay reportes recientes para mostrar.</p>
            <?php endif; ?>
            <div style="text-align: right; margin-top: 15px;">
                <a href="reportes.php" class="btn">Ver Todos los Reportes <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="activity-card">
            <h3>C:ltimas Reparaciones</h3>
            <?php if (!empty($ultimas_reparaciones)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Sensor</th>
                            <th>TC)cnico</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_reparaciones as $reparacion): ?>
                            <tr>
                                <td data-label="Sensor"><?php echo htmlspecialchars($reparacion['sensor_nombre']); ?></td>
                                <td data-label="TC)cnico"><?php echo htmlspecialchars($reparacion['tecnico_nombre']); ?></td>
                                <td data-label="Inicio"><?php echo htmlspecialchars($reparacion['fecha_inicio']); ?></td>
                                <td data-label="Fin"><?php echo htmlspecialchars($reparacion['fecha_fin'] ?? 'N/A'); ?></td>
                                <td data-label="Estado">
                                    <span class="status-badge <?php echo htmlspecialchars($reparacion['estado']); ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $reparacion['estado']))); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay reparaciones recientes para mostrar.</p>
            <?php endif; ?>
            <div style="text-align: right; margin-top: 15px;">
                <a href="reparaciones.php" class="btn">Ver Todas las Reparaciones <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos especC-ficos para el dashboard */
.dashboard-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
    padding: 20px;
    background-color: #f0f8ff;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
}

.summary-card {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e0e0e0;
    position: relative;
    overflow: hidden;
}

.summary-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.summary-card h3 {
    margin-top: 0;
    font-size: 1.4em;
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 700;
}

.summary-card p {
    font-size: 2.8em;
    font-weight: 800;
    margin: 0;
    color: #3498db;
}

.summary-card i {
    font-size: 3em;
    position: absolute;
    bottom: 10px;
    right: 15px;
    opacity: 0.1;
    color: #2c3e50;
    transition: transform 0.3s ease;
}

.summary-card:hover i {
    transform: scale(1.1);
}

/* Colores especC-ficos para las tarjetas de resumen */
.summary-card.functional p { color: #27ae60; }
.summary-card.functional i { color: #27ae60; }
.summary-card.damaged p { color: #e74c3c; }
.summary-card.damaged i { color: #e74c3c; }
.summary-card.repairing p { color: #f39c12; }
.summary-card.repairing i { color: #f39c12; }
.summary-card.total p { color: #34495e; }
.summary-card.total i { color: #34495e; }


.recent-activities {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

.activity-card {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #eee;
}

.activity-card h3 {
    color: #2c3e50;
    margin-top: 0;
    margin-bottom: 25px;
    font-size: 1.8em;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 0.85em;
    font-weight: 600;
    color: white;
}

.status-badge.funcional { background-color: #27ae60; }
.status-badge.averiado { background-color: #e74c3c; }
.status-badge.en_reparacion { background-color: #f39c12; }
.status-badge.finalizado { background-color: #3498db; }


@media (min-width: 992px) {
    .recent-activities {
        grid-template-columns: 1fr 1fr; /* Dos columnas en pantallas grandes */
    }
}

@media (max-width: 768px) {
    .dashboard-summary {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Incluye el pie de pC!gina
include_once 'footer.php';
?>
