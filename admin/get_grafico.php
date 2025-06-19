
<?php
include_once '../conexion.php';
header('Content-Type: application/json');

$sensor1 = $_POST['sensor1'] ?? null;
$sensor2 = $_POST['sensor2'] ?? null;
$tipoGrafica = $_POST['tipoGrafica'] ?? 'historica';
$anio = $_POST['anio'] ?? date('Y');
$periodo = $_POST['periodo'] ?? 'dia';
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;

$response = [
    'labels' => [],
    'datasets' => [],
    'titulo' => ''
];

function formatoFecha($periodo) {
    return match ($periodo) {
        'dia' => '%Y-%m-%d',
        'semana' => '%Y-%u',
        'mes' => '%Y-%m',
        'anual' => '%Y',
        default => '%Y-%m-%d'
    };
}

function obtenerDatosCaudal($conexion, $sensor_id, $anio, $periodo) {
    $formato = formatoFecha($periodo);
    $stmt = $conexion->prepare("
        SELECT DATE_FORMAT(fecha, ?) AS periodo, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND YEAR(fecha) = ?
        GROUP BY periodo ORDER BY periodo
    ");
    $stmt->bind_param('sii', $formato, $sensor_id, $anio);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['periodo'];
        $datos[] = round($row['promedio'], 2);
    }
    return [$labels, $datos];
}

function obtenerPorFechas($conexion, $sensor_id, $inicio, $fin) {
    $stmt = $conexion->prepare("
        SELECT DATE(fecha) AS dia, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND fecha BETWEEN ? AND ?
        GROUP BY dia ORDER BY dia
    ");
    $stmt->bind_param('iss', $sensor_id, $inicio, $fin);
    $stmt->execute();
    $res = $stmt->get_result();

    $labels = [];
    $datos = [];
    while ($r = $res->fetch_assoc()) {
        $labels[] = $r['dia'];
        $datos[] = round($r['promedio'], 2);
    }
    return [$labels, $datos];
}

if ($tipoGrafica === 'comparacion_sensores' && $sensor1 && $sensor2) {
    [$l1, $d1] = obtenerDatosCaudal($conexion, $sensor1, $anio, $periodo);
    [$l2, $d2] = obtenerDatosCaudal($conexion, $sensor2, $anio, $periodo);
    $response['labels'] = array_unique(array_merge($l1, $l2));
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $d1];
    $response['datasets'][] = ['label' => 'Sensor 2', 'data' => $d2];
    $response['titulo'] = 'Comparación entre sensores';
} elseif ($tipoGrafica === 'comparacion_fechas' && $sensor1 && $fecha_inicio && $fecha_fin) {
    [$labels, $datos] = obtenerPorFechas($conexion, $sensor1, $fecha_inicio, $fecha_fin);
    $response['labels'] = $labels;
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $datos];
    $response['titulo'] = 'Comparación por fechas';
} elseif ($tipoGrafica === 'comparacion_temporadas' && $sensor1) {
    // Definir rangos fijos simulados para lluvias y sequía
    $lluvia_ini = "$anio-02-01";
    $lluvia_fin = "$anio-04-30";
    $sequia_ini = "$anio-08-01";
    $sequia_fin = "$anio-10-31";

    [$l1, $d1] = obtenerPorFechas($conexion, $sensor1, $lluvia_ini, $lluvia_fin);
    [$l2, $d2] = obtenerPorFechas($conexion, $sensor1, $sequia_ini, $sequia_fin);

    $response['labels'] = ['Temporada de lluvia', 'Temporada de sequía'];
    $response['datasets'][] = [
        'label' => 'Promedios por temporada',
        'data' => [round(array_sum($d1) / max(count($d1),1), 2), round(array_sum($d2) / max(count($d2),1), 2)]
    ];
    $response['titulo'] = 'Comparación por temporadas';
} elseif ($tipoGrafica === 'historica' && $sensor1) {
    [$labels, $datos] = obtenerDatosCaudal($conexion, $sensor1, $anio, $periodo);
    $response['labels'] = $labels;
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $datos];
    $response['titulo'] = 'Histórico por sensor';
}

echo json_encode($response);
?>

<?php
include_once '../conexion.php';
header('Content-Type: application/json');

$sensor1 = $_POST['sensor1'] ?? null;
$sensor2 = $_POST['sensor2'] ?? null;
$tipoGrafica = $_POST['tipoGrafica'] ?? 'historica';
$anio = $_POST['anio'] ?? date('Y');
$periodo = $_POST['periodo'] ?? 'dia';
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;

$response = [
    'labels' => [],
    'datasets' => [],
    'titulo' => ''
];

function formatoFecha($periodo) {
    return match ($periodo) {
        'dia' => '%Y-%m-%d',
        'semana' => '%Y-%u',
        'mes' => '%Y-%m',
        'anual' => '%Y',
        default => '%Y-%m-%d'
    };
}

function obtenerDatosCaudal($conexion, $sensor_id, $anio, $periodo) {
    $formato = formatoFecha($periodo);
    $stmt = $conexion->prepare("
        SELECT DATE_FORMAT(fecha, ?) AS periodo, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND YEAR(fecha) = ?
        GROUP BY periodo ORDER BY periodo
    ");
    $stmt->bind_param('sii', $formato, $sensor_id, $anio);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['periodo'];
        $datos[] = round($row['promedio'], 2);
    }
    return [$labels, $datos];
}

function obtenerPorFechas($conexion, $sensor_id, $inicio, $fin) {
    $stmt = $conexion->prepare("
        SELECT DATE(fecha) AS dia, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND fecha BETWEEN ? AND ?
        GROUP BY dia ORDER BY dia
    ");
    $stmt->bind_param('iss', $sensor_id, $inicio, $fin);
    $stmt->execute();
    $res = $stmt->get_result();

    $labels = [];
    $datos = [];
    while ($r = $res->fetch_assoc()) {
        $labels[] = $r['dia'];
        $datos[] = round($r['promedio'], 2);
    }
    return [$labels, $datos];
}

if ($tipoGrafica === 'comparacion_sensores' && $sensor1 && $sensor2) {
    [$l1, $d1] = obtenerDatosCaudal($conexion, $sensor1, $anio, $periodo);
    [$l2, $d2] = obtenerDatosCaudal($conexion, $sensor2, $anio, $periodo);
    $response['labels'] = array_unique(array_merge($l1, $l2));
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $d1];
    $response['datasets'][] = ['label' => 'Sensor 2', 'data' => $d2];
    $response['titulo'] = 'Comparación entre sensores';
} elseif ($tipoGrafica === 'comparacion_fechas' && $sensor1 && $fecha_inicio && $fecha_fin) {
    [$labels, $datos] = obtenerPorFechas($conexion, $sensor1, $fecha_inicio, $fecha_fin);
    $response['labels'] = $labels;
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $datos];
    $response['titulo'] = 'Comparación por fechas';
} elseif ($tipoGrafica === 'comparacion_temporadas' && $sensor1) {
    // Definir rangos fijos simulados para lluvias y sequía
    $lluvia_ini = "$anio-02-01";
    $lluvia_fin = "$anio-04-30";
    $sequia_ini = "$anio-08-01";
    $sequia_fin = "$anio-10-31";

    [$l1, $d1] = obtenerPorFechas($conexion, $sensor1, $lluvia_ini, $lluvia_fin);
    [$l2, $d2] = obtenerPorFechas($conexion, $sensor1, $sequia_ini, $sequia_fin);

    $response['labels'] = ['Temporada de lluvia', 'Temporada de sequía'];
    $response['datasets'][] = [
        'label' => 'Promedios por temporada',
        'data' => [round(array_sum($d1) / max(count($d1),1), 2), round(array_sum($d2) / max(count($d2),1), 2)]
    ];
    $response['titulo'] = 'Comparación por temporadas';
} elseif ($tipoGrafica === 'historica' && $sensor1) {
    [$labels, $datos] = obtenerDatosCaudal($conexion, $sensor1, $anio, $periodo);
    $response['labels'] = $labels;
    $response['datasets'][] = ['label' => 'Sensor 1', 'data' => $datos];
    $response['titulo'] = 'Histórico por sensor';
}

echo json_encode($response);
?>
