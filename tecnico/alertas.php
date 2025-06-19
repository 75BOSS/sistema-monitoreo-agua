<?php
// alertas.php - VisualizaciC3n de Alertas para el rol tC)cnico

include_once 'header.php'; // Incluye el encabezado y la verificaciC3n de autenticaciC3n
require_once '../config.php'; // AsegC:rate de que la ruta sea correcta

// --- LC3gica para filtrar alertas ---
$filter_sensor_id = $_GET['sensor_id'] ?? '';
$filter_tipo_alerta = $_GET['tipo_alerta'] ?? '';
$filter_fecha_inicio = $_GET['fecha_inicio'] ?? '';
$filter_fecha_fin = $_GET['fecha_fin'] ?? '';
$filter_estado = $_GET['estado'] ?? 'activa'; // Por defecto, mostrar solo activas

$sql_alertas = "SELECT a.id, a.reporte_id, a.tipo_alerta, a.descripcion, a.fecha_alerta, a.estado,
                       s.nombre as sensor_nombre, s.estado as sensor_estado,
                       r.tipo_reporte as reporte_origen_tipo, r.fecha as reporte_origen_fecha, r.hora as reporte_origen_hora
                FROM alertas a
                JOIN sensores s ON a.sensor_id = s.id
                LEFT JOIN reportes r ON a.reporte_id = r.id
                WHERE 1=1";

$params = [];
$types = '';

if (!empty($filter_sensor_id)) {
    $sql_alertas .= " AND a.sensor_id = ?";
    $params[] = $filter_sensor_id;
    $types .= 'i';
}
if (!empty($filter_tipo_alerta)) {
    $sql_alertas .= " AND a.tipo_alerta LIKE ?";
    $params[] = '%' . $filter_tipo_alerta . '%';
    $types .= 's';
}
if (!empty($filter_fecha_inicio)) {
    $sql_alertas .= " AND a.fecha_alerta >= ?";
    $params[] = $filter_fecha_inicio . ' 00:00:00';
    $types .= 's';
}
if (!empty($filter_fecha_fin)) {
    $sql_alertas .= " AND a.fecha_alerta <= ?";
    $params[] = $filter_fecha_fin . ' 23:59:59';
    $types .= 's';
}
// Siempre aplicar filtro por estado, incluso si es 'todas'
if ($filter_estado !== 'todas') {
    $sql_alertas .= " AND a.estado = ?";
    $params[] = $filter_estado;
    $types .= 's';
}

$sql_alertas .= " ORDER BY a.fecha_alerta DESC";

$stmt_alertas = $conn->prepare($sql_alertas);
if (!empty($params)) {
    $stmt_alertas->bind_param($types, ...$params);
}
$stmt_alertas->execute();
$result_alertas = $stmt_alertas->get_result();

$alertas = [];
if ($result_alertas->num_rows > 0) {
    while ($row = $result_alertas->fetch_assoc()) {
        $alertas[] = $row;
    }
}
$stmt_alertas->close();

// Obtener lista de sensores para el filtro
$sensores_for_filter = [];
$sql_sensores_filter = "SELECT id, nombre FROM sensores ORDER BY nombre ASC";
$result_sensores_filter = $conn->query($sql_sensores_filter);
if ($result_sensores_filter->num_rows > 0) {
    while ($row = $result_sensores_filter->fetch_assoc()) {
        $sensores_for_filter[] = $row;
    }
}
$conn->close();
?>

<div class="content-section">
    <h2><i class="fas fa-bell"></i> VisualizaciC3n de Alertas</h2>

    <!-- SecciC3n de Filtros -->
    <form method="GET" action="alertas.php" class="filter-controls">
        <div class="form-group">
            <label for="filter_estado">Estado:</label>
            <select id="filter_estado" name="estado">
                <option value="activa" <?php echo ($filter_estado == 'activa') ? 'selected' : ''; ?>>Activas</option>
                <option value="resuelta" <?php echo ($filter_estado == 'resuelta') ? 'selected' : ''; ?>>Resueltas</option>
                <option value="todas" <?php echo ($filter_estado == 'todas') ? 'selected' : ''; ?>>Todas</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_sensor">Sensor:</label>
            <select id="filter_sensor" name="sensor_id">
                <option value="">Todos</option>
                <?php foreach ($sensores_for_filter as $sensor): ?>
                    <option value="<?php echo htmlspecialchars($sensor['id']); ?>" <?php echo ($filter_sensor_id == $sensor['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sensor['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_tipo_alerta">Tipo de Alerta:</label>
            <input type="text" id="filter_tipo_alerta" name="tipo_alerta" value="<?php echo htmlspecialchars($filter_tipo_alerta); ?>" placeholder="Ej: Caudal Bajo">
        </div>
        <div class="form-group">
            <label for="filter_fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="filter_fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($filter_fecha_inicio); ?>">
        </div>
        <div class="form-group">
            <label for="filter_fecha_fin">Fecha Fin:</label>
            <input type="date" id="filter_fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($filter_fecha_fin); ?>">
        </div>
        <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtrar</button>
        <a href="alertas.php" class="btn btn-secondary">Limpiar Filtros</a>
    </form>

    <!-- Tabla de listado de alertas -->
    <?php if (!empty($alertas)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Alerta</th>
                    <th>Sensor</th>
                    <th>Tipo de Alerta</th>
                    <th>DescripciC3n</th>
                    <th>Fecha Alerta</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alertas as $alerta): ?>
                    <tr>
                        <td data-label="ID Alerta"><?php echo htmlspecialchars($alerta['id']); ?></td>
                        <td data-label="Sensor"><?php echo htmlspecialchars($alerta['sensor_nombre']); ?></td>
                        <td data-label="Tipo de Alerta"><?php echo htmlspecialchars($alerta['tipo_alerta']); ?></td>
                        <td data-label="DescripciC3n"><?php echo htmlspecialchars($alerta['descripcion']); ?></td>
                        <td data-label="Fecha Alerta"><?php echo htmlspecialchars($alerta['fecha_alerta']); ?></td>
                        <td data-label="Estado">
                            <span class="status-badge <?php echo htmlspecialchars($alerta['estado']); ?>">
                                <?php echo htmlspecialchars(ucfirst($alerta['estado'])); ?>
                            </span>
                        </td>
                        <td data-label="Acciones">
                            <button class="btn btn-info btn-small" onclick="showAlertDialog(<?php echo htmlspecialchars(json_encode($alerta)); ?>)"><i class="fas fa-search"></i> Ver Detalles</button>
                            <?php if ($alerta['estado'] === 'activa'): ?>
                                <button class="btn btn-success btn-small" onclick="confirmResolveAlert(<?php echo $alerta['id']; ?>)"><i class="fas fa-check"></i> Marcar Resuelta</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No se encontraron alertas con los criterios de bCzsqueda.</p>
    <?php endif; ?>
</div>

<!-- Modal para Detalles de la Alerta -->
<div id="alertDialog" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal('alertDialog')">&times;</span>
        <h3><i class="fas fa-info-circle"></i> Detalles de la Alerta <span id="detailAlertId"></span></h3>
        <div id="alertDetailsContent">
            <!-- Los detalles se cargarC!n aquC- vC-a JavaScript -->
        </div>
        <div class="modal-buttons" style="text-align: right; margin-top: 20px;">
            <button class="btn btn-secondary" onclick="closeModal('alertDialog')">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal de ConfirmaciC3n para Resolver Alerta -->
<div id="confirmResolveModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal('confirmResolveModal')">&times;</span>
        <h3><i class="fas fa-question-circle"></i> Confirmar ResoluciC3n de Alerta</h3>
        <p>¿EstC!s seguro de que quieres marcar esta alerta como 'Resuelta'?</p>
        <form method="POST" action="alertas.php" id="formResolveAlert">
            <input type="hidden" name="action" value="resolve_alert">
            <input type="hidden" name="alert_id_to_resolve" id="alertIdToResolve">
            <div class="form-actions" style="justify-content: space-around;">
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> SC-, Marcar Resuelta</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmResolveModal')"><i class="fas fa-times"></i> Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos especC-ficos para alertas.php */
.no-results {
    text-align: center;
    padding: 20px;
    font-style: italic;
    color: #777;
}

/* Modal styles (repetido por si este archivo es accedido directamente) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
    justify-content: center;
    align-items: center;
    padding-top: 50px;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 30px;
    border: 1px solid #888;
    width: 80%; /* Could be more responsive */
    max-width: 600px;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    position: relative;
    animation: fadeIn 0.3s ease-out;
}

.close-button {
    color: #aaa;
    float: right;
    font-size: 32px;
    font-weight: bold;
    position: absolute;
    right: 20px;
    top: 10px;
    cursor: pointer;
}

.close-button:hover,
.close-button:focus {
    color: #333;
    text-decoration: none;
}

.modal-content h3 {
    color: #2c3e50;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 1.8em;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
    display: flex;
    align-items: center;
}

.modal-content h3 i {
    margin-right: 10px;
    color: #3498db;
}

/* Insignias de estado para alertas */
.status-badge.activa {
    background-color: #e74c3c; /* Rojo para alertas activas */
}
.status-badge.resuelta {
    background-color: #27ae60; /* Verde para alertas resueltas */
}
/* AsegC:rate de que el estilo base de .status-badge estC! en style.css */

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 20px;
    }
}
</style>

<script>
    // FunciC3n para abrir el modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    // FunciC3n para cerrar el modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // FunciC3n para mostrar los detalles de la alerta en un modal
    function showAlertDialog(alerta) {
        document.getElementById('detailAlertId').textContent = `#${alerta.id}`;
        const detailsContent = document.getElementById('alertDetailsContent');
        let htmlContent = `
            <p><strong>Sensor:</strong> ${alerta.sensor_nombre}</p>
            <p><strong>Tipo de Alerta:</strong> ${alerta.tipo_alerta}</p>
            <p><strong>DescripciC3n:</strong> ${alerta.descripcion ? alerta.descripcion.replace(/\n/g, '<br>') : 'N/A'}</p>
            <p><strong>Fecha y Hora de Alerta:</strong> ${alerta.fecha_alerta}</p>
            <p><strong>Estado:</strong> <span class="status-badge ${alerta.estado}">${alerta.estado.charAt(0).toUpperCase() + alerta.estado.slice(1)}</span></p>
        `;

        if (alerta.reporte_id) {
            htmlContent += `<p><strong>Reporte Origen:</strong> ID ${alerta.reporte_id} (${alerta.reporte_origen_tipo} - ${alerta.reporte_origen_fecha} ${alerta.reporte_origen_hora})</p>`;
            // PodrC-as aC1adir un botC3n para ver los detalles del reporte origen aquC- si fuera necesario
            // <button class="btn btn-info btn-small" onclick="window.location.href='reportes.php?reporte_id=${alerta.reporte_id}'">Ver Reporte Origen</button>
        } else {
            htmlContent += `<p><strong>Reporte Origen:</strong> No asociado o eliminado.</p>`;
        }

        detailsContent.innerHTML = htmlContent;
        openModal('alertDialog');
    }

    // FunciC3n para confirmar y marcar como resuelta
    function confirmResolveAlert(alertId) {
        document.getElementById('alertIdToResolve').value = alertId;
        openModal('confirmResolveModal');
    }

    // Cerrar modales si se hace clic fuera de ellos
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

<?php include_once 'footer.php'; // Incluye el pie de pC!gina ?>
