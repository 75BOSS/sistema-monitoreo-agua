<?php
// registro_caudal.php - Formulario para registrar reportes de caudal

include_once 'header.php'; // Incluye el encabezado y la verificaciC3n de autenticaciC3n
require_once '../config.php'; // AsegC:rate de que la ruta sea correcta

$message = ''; // Variable para mensajes de C)xito/error

// LC3gica para procesar el formulario de registro de caudal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sensor_id = escape_data($_POST['sensor_id']);
    $fecha = escape_data($_POST['fecha']);
    $hora = escape_data($_POST['hora']);
    $caudal_lps = escape_data($_POST['caudal_lps']);
    $latitud = escape_data($_POST['latitud']);
    $longitud = escape_data($_POST['longitud']);
    $observaciones = escape_data($_POST['observaciones']);
    $usuario_id = $current_user_id; // Obtener el ID del usuario de la sesiC3n

    $sql_insert = "INSERT INTO reportes (sensor_id, usuario_id, tipo_reporte, caudal_lps, fecha, hora, latitud, longitud, observaciones)
                   VALUES ('$sensor_id', '$usuario_id', 'caudal', '$caudal_lps', '$fecha', '$hora', '$latitud', '$longitud', '$observaciones')";

    if ($conn->query($sql_insert) === TRUE) {
        $message = "Reporte de caudal registrado con C)xito.";
        echo "<script>showMessage('$message', 'success');</script>";
    } else {
        $message = "Error al registrar el reporte de caudal: " . $conn->error;
        echo "<script>showMessage('$message', 'error');</script>";
    }
    $conn->close();
}

// Obtener la lista de sensores para el desplegable
$sensores = [];
$sql_sensores = "SELECT id, nombre FROM sensores ORDER BY nombre ASC";
$result_sensores = $conn->query($sql_sensores);
if ($result_sensores->num_rows > 0) {
    while ($row = $result_sensores->fetch_assoc()) {
        $sensores[] = $row;
    }
}
$conn->close();
?>

<div class="content-section">
    <h2><i class="fas fa-tint"></i> Registrar Reporte de Caudal</h2>

    <form method="POST" action="registro_caudal.php">
        <div class="form-group">
            <label for="sensor_id">Sensor Asociado:</label>
            <select id="sensor_id" name="sensor_id" required>
                <option value="">Selecciona un sensor</option>
                <?php foreach ($sensores as $sensor): ?>
                    <option value="<?php echo htmlspecialchars($sensor['id']); ?>"><?php echo htmlspecialchars($sensor['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" value="<?php echo date('H:i'); ?>" required>
        </div>

        <div class="form-group">
            <label for="caudal_lps">Caudal en LPS:</label>
            <input type="number" step="0.01" id="caudal_lps" name="caudal_lps" placeholder="Ej: 25.50" required>
        </div>

        <div class="form-group">
            <label for="latitud">Coordenada Latitud:</label>
            <input type="number" step="0.0000001" id="latitud" name="latitud" placeholder="Ej: -0.22985" required>
        </div>

        <div class="form-group">
            <label for="longitud">Coordenada Longitud:</label>
            <input type="number" step="0.0000001" id="longitud" name="longitud" placeholder="Ej: -78.52495" required>
        </div>

        <div class="form-group">
            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" name="observaciones" rows="4" placeholder="Notas adicionales sobre el reporte..."></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Caudal</button>
        </div>
    </form>
</div>

<style>
/* Estilos especC-ficos para registro_caudal.php */
.form-group label {
    font-size: 1.1em;
    margin-bottom: 8px;
    color: #444;
}

.form-group input, .form-group select, .form-group textarea {
    padding: 12px;
    border: 1px solid #dcdcdc;
    border-radius: 8px;
    font-size: 1em;
    box-shadow: inset 0 1px 4px rgba(0,0,0,0.05);
}

.form-actions {
    margin-top: 30px;
    text-align: center;
}

.form-actions .btn {
    min-width: 180px;
    padding: 12px 25px;
    font-size: 1.1em;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.form-actions .btn:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}
</style>

<?php include_once 'footer.php'; // Incluye el pie de pC!gina ?>
