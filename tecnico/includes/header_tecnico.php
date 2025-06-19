<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'tecnico') {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard_tecnico.php">Monitoreo Técnico</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard_tecnico.php">Sensores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="alertas.php">Alertas</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                Técnico: <?= htmlspecialchars($_SESSION['nombre']) ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>
