<?php require_once "../includes/header.php";
$result = $conn->query("SELECT r.*, s.nombre as sensor_nombre FROM reportes r 
    JOIN sensores s ON r.sensor_id = s.id 
    WHERE r.caudal_lps < 0.86
    ORDER BY fecha DESC, hora DESC");
?>
<h2>Alertas de Caudal Bajo</h2>
<table class="table table-danger table-striped">
    <thead>
        <tr>
            <th>Fecha</th><th>Hora</th><th>Sensor</th><th>Caudal</th><th>Ubicación</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["fecha"] ?></td>
            <td><?= $row["hora"] ?></td>
            <td><?= $row["sensor_nombre"] ?></td>
            <td><strong><?= $row["caudal_lps"] ?></strong> l/s</td>
            <td><?= $row["comunidad"] ?? '---' ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require_once "../includes/footer.php"; ?>
