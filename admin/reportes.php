<?php require_once "../includes/header.php";
$result = $conn->query("SELECT r.*, s.nombre as sensor_nombre, u.nombre as usuario_nombre FROM reportes r 
    JOIN sensores s ON r.sensor_id = s.id 
    JOIN usuarios u ON r.usuario_id = u.id 
    ORDER BY fecha DESC, hora DESC");
?>
<h2>Reportes Globales</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Fecha</th><th>Hora</th><th>Sensor</th><th>Usuario</th><th>Caudal</th><th>Calidad</th><th>Obs.</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["fecha"] ?></td>
            <td><?= $row["hora"] ?></td>
            <td><?= $row["sensor_nombre"] ?></td>
            <td><?= $row["usuario_nombre"] ?></td>
            <td><?= $row["caudal_lps"] ?> l/s</td>
            <td>
                <?= $row["turbidez"] ? "Turbio " : "" ?>
                <?= $row["olor"] ? "Con olor " : "" ?>
                <?= $row["color"] ? "Color anormal " : "" ?>
                <?= $row["residuos"] ? "Residuos" : "" ?>
            </td>
            <td><?= $row["observaciones"] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require_once "../includes/footer.php"; ?>
