<?php
include_once '../conexion.php';

header('Content-Type: application/json');

$sensor1 = $_POST['sensor1'] ?? null;
$sensor2 = $_POST['sensor2'] ?? null;
$tipoGrafica = $_POST['tipoGrafica'] ?? 'historica';
$anio = $_POST['anio'] ?? date('Y');
$periodo = $_POST['periodo'] ?? 'dia';

function obtenerDatos($conexion, $sensor_id, $anio, $periodo) {
    $formatoFecha = match ($periodo) {
        'dia' => '%Y-%m-%d',
        'semana' => '%Y-%u',
        'mes' => '%Y-%m',
        default => '%Y-%m-%d'
    };

    $query = $conexion->prepare("
        SELECT DATE_FORMAT(fecha, ?) AS periodo, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND YEAR(fecha) = ?
        GROUP BY periodo
        ORDER BY periodo ASC
    ");
    $query->bind_param('sii', $formatoFecha, $sensor_id, $anio);
    $query->execute();
    $result = $query->get_result();

    $labels = [];
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['periodo'];
        $data[] = round($row['promedio'], 2);
    }

    return [$labels, $data];
}

$response = [
    'labels' => [],
    'datasets' => [],
    'titulo' => ''
];

if ($tipoGrafica === 'comparacion_sensores' && $sensor1 && $sensor2) {
    [$labels1, $data1] = obtenerDatos($conexion, $sensor1, $anio, $periodo);
    [$labels2, $data2] = obtenerDatos($conexion, $sensor2, $anio, $periodo);

    $response['labels'] = array_unique(array_merge($labels1, $labels2));
    $response['datasets'][] = [
        'label' => 'Sensor 1',
        'data' => $data1,
        'borderColor' => 'rgba(54, 162, 235, 1)',
        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
        'fill' => false
    ];
    $response['datasets'][] = [
        'label' => 'Sensor 2',
        'data' => $data2,
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        'fill' => false
    ];
    $response['titulo'] = 'Comparación entre sensores';
} elseif ($sensor1) {
    [$labels, $data] = obtenerDatos($conexion, $sensor1, $anio, $periodo);
    $response['labels'] = $labels;
    $response['datasets'][] = [
        'label' => 'Sensor',
        'data' => $data,
        'borderColor' => 'rgba(75, 192, 192, 1)',
        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
        'fill' => false
    ];
    $titulos = [
        'historica' => 'Histórico del sensor',
        'comparacion_fechas' => 'Comparación por fechas',
        'comparacion_temporadas' => 'Temporadas lluvia vs. sequía'
    ];
    $response['titulo'] = $titulos[$tipoGrafica] ?? 'Gráfica del sensor';
}


if ($tipoGrafica === 'comparacion_fechas' && $sensor1 && $fecha_inicio && $fecha_fin) {
    $formato = formatoFecha($periodo);
    $stmt = $conexion->prepare("
        SELECT DATE_FORMAT(fecha, ?) as periodo, AVG(caudal_lps) as caudal
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal'
        AND fecha BETWEEN ? AND ?
        GROUP BY periodo
        ORDER BY periodo
    ");
    $stmt->execute([$formato, $sensor1, $fecha_inicio, $fecha_fin]);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['labels'] = array_column($datos, 'periodo');
    $response['datasets'][] = [
        'label' => 'Caudal Promedio',
        'data' => array_column($datos, 'caudal')
    ];
    $response['titulo'] = 'Caudal entre fechas seleccionadas';
}


echo json_encode($response);
