<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}

include '../conexion.php'; // tu archivo de conexión

// Conteos rápidos
$sensores = mysqli_query($conn, "SELECT COUNT(*) as total FROM sensores");
$averiados = mysqli_query($conn, "SELECT COUNT(*) as total FROM sensores WHERE estado = 'averiado'");
$reparacion = mysqli_query($conn, "SELECT COUNT(*) as total FROM reparaciones WHERE estado = 'en_reparacion'");
$alertas = mysqli_query($conn, "SELECT COUNT(*) as total FROM reportes WHERE (caudal_lps > 100 OR turbidez = 1 OR olor = 1 OR color = 1 OR residuos = 1)"); // ejemplo de umbral
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Bienvenido, Técnico <?= $_SESSION['usuario'] ?></h1>

    <div class="dashboard">
        <div class="card">Sensores Totales: <?= mysqli_fetch_assoc($sensores)['total'] ?></div>
        <div class="card">Averiados: <?= mysqli_fetch_assoc($averiados)['total'] ?></div>
        <div class="card">En Reparación: <?= mysqli_fetch_assoc($reparacion)['total'] ?></div>
        <div class="card">Alertas Activas: <?= mysqli_fetch_assoc($alertas)['total'] ?></div>
    </div>

    <nav>
        <a href="sensores.php">📡 Sensores</a> |
        <a href="registro_caudal.php">💧 Registro Caudal</a> |
        <a href="registro_calidad.php">🧪 Registro Calidad</a> |
        <a href="reparaciones.php">🔧 Reparaciones</a> |
        <a href="reportes.php">📊 Reportes</a> |
        <a href="alertas.php">🚨 Alertas</a> |
        <a href="perfil.php">👤 Mi Perfil</a> |
        <a href="../logout.php">⛔ Cerrar sesión</a>
    </nav>
</body>
</html>
