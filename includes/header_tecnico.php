<?php
if (!isset($_SESSION)) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Técnico</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Sensores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reparar_sensor.php">Reparar Sensor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="alertas.php">Alertas</a>
                </li>
            </ul>
            <span class="navbar-text text-white">
                Técnico: <?= $_SESSION['nombre'] ?>
                <a href="../logout.php" class="btn btn-sm btn-light ms-3">Cerrar sesión</a>
            </span>
        </div>
    </div>
</nav>
