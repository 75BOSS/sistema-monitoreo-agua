<?php
include_once '../conexion.php';
header('Content-Type: application/json');

$sensor = $_POST['sensor'] ?? null;
$tipoGrafica = $_POST['tipoGrafica'] ?? 'historica';
$anio = $_POST['anio'] ?? date('Y');
$periodo = $_POST['periodo'] ?? 'mes';
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

if ($tipoGrafica === 'historica' && $sensor) {
    $formato = formatoFecha($periodo);
    $query = $conexion->prepare("
        SELECT DATE_FORMAT(fecha, ?) AS periodo, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND YEAR(fecha) = ?
        GROUP BY periodo ORDER BY periodo ASC
    ");
    $query->bind_param('sii', $formato, $sensor, $anio);
    $query->execute();
    $result = $query->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['labels'][] = $row['periodo'];
        $response['datasets'][] = [
            'label' => 'Caudal (l/s)',
            'data' => [$row['promedio']],
            'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'fill' => false
        ];
    }
    $response['titulo'] = 'Histórico de caudal';
}

elseif ($tipoGrafica === 'comparacion_fechas' && $sensor && $fecha_inicio && $fecha_fin) {
    $query = $conexion->prepare("
        SELECT fecha, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND fecha BETWEEN ? AND ?
        GROUP BY fecha ORDER BY fecha ASC
    ");
    $query->bind_param('iss', $sensor, $fecha_inicio, $fecha_fin);
    $query->execute();
    $result = $query->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $response['labels'][] = $row['fecha'];
        $data[] = round($row['promedio'], 2);
    }
    $response['datasets'][] = [
        'label' => 'Caudal (l/s)',
        'data' => $data,
        'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
        'borderColor' => 'rgba(255, 206, 86, 1)',
        'fill' => false
    ];
    $response['titulo'] = 'Comparación por fechas';
}

elseif ($tipoGrafica === 'comparacion_temporadas' && $sensor) {
    $query = $conexion->prepare("
        SELECT MONTH(fecha) AS mes, AVG(caudal_lps) AS promedio
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'caudal' AND YEAR(fecha) = ?
        GROUP BY mes ORDER BY mes ASC
    ");
    $query->bind_param('ii', $sensor, $anio);
    $query->execute();
    $result = $query->get_result();

    $lluvia = []; $sequía = []; $labels = [];
    while ($row = $result->fetch_assoc()) {
        $mes = (int)$row['mes'];
        $labels[] = date('F', mktime(0, 0, 0, $mes, 10));
        if ($mes >= 12 || $mes <= 5) {
            $lluvia[] = round($row['promedio'], 2);
            $sequía[] = null;
        } else {
            $sequía[] = round($row['promedio'], 2);
            $lluvia[] = null;
        }
    }

    $response['labels'] = $labels;
    $response['datasets'][] = [
        'label' => 'Temporada de lluvia',
        'data' => $lluvia,
        'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
        'borderColor' => 'rgba(75, 192, 192, 1)',
        'fill' => false
    ];
    $response['datasets'][] = [
        'label' => 'Temporada de sequía',
        'data' => $sequía,
        'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'fill' => false
    ];
    $response['titulo'] = 'Comparación entre temporadas';
}

elseif ($tipoGrafica === 'calidad' && $sensor) {
    $query = $conexion->prepare("
        SELECT fecha,
            SUM(turbidez) AS turbidez,
            SUM(olor) AS olor,
            SUM(color) AS color,
            SUM(residuos) AS residuos
        FROM reportes
        WHERE sensor_id = ? AND tipo_reporte = 'calidad' AND YEAR(fecha) = ?
        GROUP BY fecha ORDER BY fecha ASC
    ");
    $query->bind_param('ii', $sensor, $anio);
    $query->execute();
    $result = $query->get_result();

    $labels = []; $turbidez = []; $olor = []; $color = []; $residuos = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['fecha'];
        $turbidez[] = (int)$row['turbidez'];
        $olor[] = (int)$row['olor'];
        $color[] = (int)$row['color'];
        $residuos[] = (int)$row['residuos'];
    }

    $response['labels'] = $labels;
    $response['datasets'] = [
        ['label' => 'Turbidez', 'data' => $turbidez, 'backgroundColor' => 'rgba(255, 99, 132, 0.5)'],
        ['label' => 'Olor', 'data' => $olor, 'backgroundColor' => 'rgba(255, 159, 64, 0.5)'],
        ['label' => 'Color', 'data' => $color, 'backgroundColor' => 'rgba(54, 162, 235, 0.5)'],
        ['label' => 'Residuos', 'data' => $residuos, 'backgroundColor' => 'rgba(153, 102, 255, 0.5)']
    ];
    $response['titulo'] = 'Reporte de calidad del agua';
}

echo json_encode($response);
