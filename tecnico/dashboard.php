
<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header_tecnico.php';

$estado_filtro = $_GET['estado'] ?? '';

$where = '';
if ($estado_filtro) {
    $where = "WHERE estado = ?";
    $query = $conexion->prepare("SELECT * FROM sensores $where");
    $query->bind_param('s', $estado_filtro);
} else {
    $query = $conexion->prepare("SELECT * FROM sensores");
}
$query->execute();
$resultado = $query->get_result();
?>

<div class="container mt-5">
    <h2 class="mb-4">Sensores Registrados</h2>

    <form method="GET" class="mb-3">
        <label for="estado">Filtrar por estado:</label>
        <select name="estado" id="estado" onchange="this.form.submit()" class="form-select w-auto d-inline-block ms-2">
            <option value="">Todos</option>
            <option value="funcional" <?= $estado_filtro === 'funcional' ? 'selected' : '' ?>>Funcional</option>
            <option value="averiado" <?= $estado_filtro === 'averiado' ? 'selected' : '' ?>>Averiado</option>
            <option value="en_reparacion" <?= $estado_filtro === 'en_reparacion' ? 'selected' : '' ?>>En Reparación</option>
        </select>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($sensor = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $sensor['id'] ?></td>
                <td><?= $sensor['nombre'] ?></td>
                <td><?= $sensor['comunidad'] ?>, <?= $sensor['ciudad'] ?>, <?= $sensor['provincia'] ?></td>
                <td><?= ucfirst($sensor['estado']) ?></td>
                <td>
                    <?php if ($sensor['estado'] === 'averiado'): ?>
                        <form method="POST" action="reparar_sensor.php" class="d-inline">
                            <input type="hidden" name="sensor_id" value="<?= $sensor['id'] ?>">
                            <button type="submit" class="btn btn-warning btn-sm">Reparar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">---</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
