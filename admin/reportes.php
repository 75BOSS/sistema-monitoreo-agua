<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include_once '../conexion.php';
include_once '../includes/header.php';

// Obtener sensores para el formulario
$sensores = $conexion->query("SELECT id, nombre FROM sensores ORDER BY nombre ASC");
?>

<div class="container mt-5">
    <h2 class="mb-4">Visualización de Reportes</h2>

    <form id="formComparar" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Sensor 1:</label>
            <select class="form-control" id="sensor1" name="sensor1" required>
                <option value="">Seleccione</option>
                <?php while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4" id="sensor2Container" style="display: none;">
            <label>Sensor 2 (para comparar):</label>
            <select class="form-control" id="sensor2" name="sensor2">
                <option value="">Seleccione</option>
                <?php
                $sensores->data_seek(0); // Reiniciar el puntero
                while ($s = $sensores->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Tipo de Gráfica:</label>
            <select class="form-control" id="tipoGrafica" name="tipoGrafica" required>
                <option value="historica">Histórica (por sensor)</option>
                <option value="comparacion_fechas">Comparación por fechas</option>
                <option value="comparacion_temporadas">Temporada lluvia/sequía</option>
                <option value="comparacion_sensores">Comparación entre sensores</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Año:</label>
            <input type="number" class="form-control" name="anio" value="<?= date('Y') ?>" required>
        </div>

        <div class="col-md-3">
            <label>Periodo:</label>
            <select class="form-control" name="periodo">
                <option value="dia">Diario</option>
                <option value="semana">Semanal</option>
                <option value="mes">Mensual</option>
<option value="anual">Anual</option>
            </select>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100" id="botonComparar">Ver gráfica</button>
        </div>
    
<div class="col-md-3" id="fechasComparacion" style="display:none;">
    <label>Fecha inicio:</label>
    <input type="date" class="form-control mb-2" id="fecha_inicio" name="fecha_inicio">
    <label>Fecha fin:</label>
    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
</div>
</form>

    <div>
        <canvas id="graficaCaudal" height="300" style="display:block;" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




<hr class="my-5">
<div class="container">
  <h4 class="mb-3">💧 Visualización Diaria de Caudales</h4>
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <input type="date" id="filtroFechaAdmin" class="form-control">
    </div>
    <div class="col-md-4">
      <select id="filtroSensorAdmin" class="form-select"></select>
    </div>
    <div class="col-md-4">
      <button class="btn btn-outline-primary w-100" onclick="cargarGraficoCaudales()">Ver gráfico</button>
    </div>
  </div>
  <canvas id="graficoCaudales" height="180"></canvas>
</div>




<?php include_once '../includes/footer.php'; ?>
