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
    </form>

    <div>
        <canvas id="graficaCaudal" height="300" style="display:block;" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('tipoGrafica').addEventListener('change', function () {
    const sensor2 = document.getElementById('sensor2Container');
    const boton = document.getElementById('botonComparar');
    if (this.value === 'comparacion_sensores') {
        sensor2.style.display = 'block';
        boton.textContent = 'Comparar sensores';
    } else {
        sensor2.style.display = 'none';
        boton.textContent = 'Ver gráfica';
    }
});


document.getElementById('formComparar').addEventListener('submit', function (e) {
    e.preventDefault();

    const tipoGrafica = document.getElementById('tipoGrafica').value;
    const sensor1 = document.getElementById('sensor1').value;
    const sensor2 = document.getElementById('sensor2')?.value || '';
    const periodo = document.querySelector('[name="periodo"]').value;
    const anio = document.querySelector('[name="anio"]').value;
    const fecha_inicio = document.getElementById('fecha_inicio')?.value || '';
    const fecha_fin = document.getElementById('fecha_fin')?.value || '';

    if (!sensor1) {
        alert('Por favor selecciona al menos un sensor.');
        return;
    }

    if (tipoGrafica === 'comparacion_sensores' && !sensor2) {
        alert('Por favor selecciona el segundo sensor para comparar.');
        return;
    }

    const datos = {
        sensor1,
        sensor2,
        tipoGrafica,
        periodo,
        anio
    };

    if (tipoGrafica === 'comparacion_fechas') {
        datos.fecha_inicio = fecha_inicio;
        datos.fecha_fin = fecha_fin;
    }

    fetch('get_grafico.php', {
        method: 'POST',
        body: new URLSearchParams(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (!data || !data.labels || data.labels.length === 0) {
            alert("No se encontraron datos para los filtros seleccionados.");
            return;
        }
        const ctx = document.getElementById('graficaCaudal').getContext('2d');
        if (window.miGrafico) window.miGrafico.destroy();

        const datasets = data.datasets.map(ds => ({ ...ds, borderWidth: 1 }));

        
const tipo = (tipoGrafica === 'comparacion_sensores') ? 'line' : 'bar';

    calcularEstadisticas(data);
        window.miGrafico = new Chart(ctx, { type: tipo,
            data: {
                labels: data.labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: data.titulo
                    },
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
});
</script>
<script>
document.getElementById('tipoGrafica').addEventListener('change', function () {
    const sensor2 = document.getElementById('sensor2Container');
    const boton = document.getElementById('botonComparar');
    if (this.value === 'comparacion_sensores') {
        sensor2.style.display = 'block';
        boton.textContent = 'Comparar sensores';
    } else {
        sensor2.style.display = 'none';
        boton.textContent = 'Ver gráfica';
    }
});

document.getElementById('formComparar').addEventListener('submit', function (e) {
    e.preventDefault();
    const tipoGrafica = document.getElementById('tipoGrafica').value;
    const sensor1 = document.getElementById('sensor1').value;
    const sensor2 = document.getElementById('sensor2').value;

    if (!sensor1) {
        alert('Por favor selecciona al menos un sensor.');
        return;
    }

    if (tipoGrafica === 'comparacion_sensores' && !sensor2) {
        alert('Por favor selecciona el segundo sensor para comparar.');
        return;
    }

    const formData = new FormData(this);

    
const tipoGrafica = document.getElementById('tipoGrafica').value;
const sensor1 = document.getElementById('sensor1')?.value || '';
const sensor2 = document.getElementById('sensor2')?.value || '';
const periodo = document.getElementById('periodo')?.value || '';
const anio = document.getElementById('anio')?.value || '';
const fecha_inicio = document.getElementById('fecha_inicio')?.value || '';
const fecha_fin = document.getElementById('fecha_fin')?.value || '';

const datos = {
  sensor1,
  sensor2,
  tipoGrafica,
  periodo,
  anio
};

if (tipoGrafica === 'por_fechas') {
  datos.fecha_inicio = fecha_inicio;
  datos.fecha_fin = fecha_fin;
}

fetch('get_grafico.php', {
  method: 'POST',
  body: new URLSearchParams(datos)
})
.then(response => response.json())
.then(data => {
        if (!data || !data.labels || data.labels.length === 0) {
            alert("No se encontraron datos para los filtros seleccionados.");
            return;
        }
  const ctx = document.getElementById('miGrafico').getContext('2d');
  if (window.miGrafico) {
    window.miGrafico.destroy();
  }

  const datasets = data.datasets.map(ds => ({ ...ds, borderWidth: 1 }));

  
const tipo = (tipoGrafica === 'comparacion_sensores') ? 'line' : 'bar';

    calcularEstadisticas(data);
        window.miGrafico = new Chart(ctx, { type: tipo,
    data: {
      labels: data.labels,
      datasets: datasets
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: data.titulo
        },
        legend: {
          display: true
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data || !data.labels || data.labels.length === 0) {
            alert("No se encontraron datos para los filtros seleccionados.");
            return;
        }
        const ctx = document.getElementById('graficaCaudal').getContext('2d');
        if (window.miGrafica) window.miGrafica.destroy();
        window.miGrafica = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: data.titulo
                    }
                }
            }
        });
    });
});
</script>


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

<script>
function cargarGraficoCaudales() {
  const fecha = document.getElementById('filtroFechaAdmin').value;
  const sensor = document.getElementById('filtroSensorAdmin').value;
  if (!fecha || !sensor) return alert('Selecciona una fecha y un sensor');

  fetch('get_caudal_dia.php', {
    method: 'POST',
    body: new URLSearchParams({ fecha, sensor })
  })
  .then(res => res.json())
  .then(data => {
    new Chart(document.getElementById('graficoCaudales'), {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: data.datasets
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Caudal registrado por hora'
          }
        }
      }
    });
  });
}

// Llenar automáticamente el selector de sensores
fetch('get_sensores.php')
  .then(res => res.json())
  .then(data => {
    const select = document.getElementById('filtroSensorAdmin');
    data.forEach(sensor => {
      const opt = document.createElement('option');
      opt.value = sensor.id;
      opt.textContent = sensor.nombre;
      select.appendChild(opt);
    });
  });
</script>


<?php include_once '../includes/footer.php'; ?>
