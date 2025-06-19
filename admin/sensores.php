<?php require_once "../includes/header.php";
$result = $conn->query("SELECT * FROM sensores");
?>
<h2>Lista de Sensores</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th><th>Ubicación</th><th>Estado</th><th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["nombre"] ?></td>
            <td><?= $row["comunidad"] ?>, <?= $row["provincia"] ?></td>
            <td><?= ucfirst($row["estado"]) ?></td>
            <td><?= $row["fecha_creacion"] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require_once "../includes/footer.php"; ?>
