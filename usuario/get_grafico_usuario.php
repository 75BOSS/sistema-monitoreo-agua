<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$sensor = $_POST['sensor1'] ?? null;
$tipo = $_POST['tipoGrafica'] ?? null;
$anio = $_POST['anio'] ?? date('Y');
$periodo = $_POST['periodo'] ?? 'mes';
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;

$response = [
    'labels' => [],
    'datasets' => [],
    'titulo' => ''
];

if (!$sensor || !$tipo) {
    echo json_encode($response);
    exit;
}

function obtenerDatos($conexion, $sensor, $condicion, $groupBy) {
    $query = "SELECT $groupBy AS etiqueta, AVG(caudal_lps) AS promedio
              FROM reportes
              WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND $condicion
              GROUP BY etiqueta
              ORDER BY etiqueta";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $sensor);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['etiqueta'];
        $data[] = round($row['promedio'], 2);
    }

    return [$labels, $data];
}

switch ($tipo) {
    case 'historica':
        $condicion = "YEAR(fecha) = $anio";
        $groupBy = match ($periodo) {
            'dia' => "DATE(fecha)",
            'semana' => "WEEK(fecha)",
            'mes' => "MONTH(fecha)",
            default => "MONTH(fecha)"
        };
        [$labels, $data] = obtenerDatos($conexion, $sensor, $condicion, $groupBy);
        $response['labels'] = $labels;
        $response['datasets'][] = [
            'label' => 'Caudal Promedio',
            'data' => $data,
            'borderColor' => 'blue',
            'fill' => false
        ];
        $response['titulo'] = "Histórico de caudal - $anio";
        break;

    case 'comparacion_fechas':
        if (!$fecha_inicio || !$fecha_fin) break;
        $condicion = "fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        $groupBy = "DATE(fecha)";
        [$labels, $data] = obtenerDatos($conexion, $sensor, $condicion, $groupBy);
        $response['labels'] = $labels;
        $response['datasets'][] = [
            'label' => 'Caudal Promedio',
            'data' => $data,
            'borderColor' => 'green',
            'fill' => false
        ];
        $response['titulo'] = "Comparación de caudal entre fechas";
        break;

    case 'comparacion_temporadas':
        $temporadas = [
            'Lluvias' => ['inicio' => "$anio-01-01", 'fin' => "$anio-03-31"],
            'Sequía' => ['inicio' => "$anio-07-01", 'fin' => "$anio-09-30"]
        ];
        $response['labels'] = array_keys($temporadas);
        foreach ($temporadas as $nombre => $rango) {
            $query = "SELECT AVG(caudal_lps) AS promedio
                      FROM reportes
                      WHERE sensor_id = ? AND tipo_reporte = 'caudal'
                      AND fecha BETWEEN ? AND ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("iss", $sensor, $rango['inicio'], $rango['fin']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $response['datasets'][0]['data'][] = round($row['promedio'], 2);
        }
        $response['datasets'][0]['label'] = 'Caudal Promedio';
        $response['datasets'][0]['backgroundColor'] = ['#4e73df', '#e74a3b'];
        $response['titulo'] = "Comparación por temporadas - $anio";
        break;
}

echo json_encode($response);
?>
