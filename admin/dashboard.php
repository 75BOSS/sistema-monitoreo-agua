<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login.php');
    exit;
}

include '../conexion.php';

$usuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$sensores = $conexion->query("SELECT COUNT(*) as total FROM sensores")->fetch_assoc()['total'];
$reportes = $conexion->query("SELECT COUNT(*) as total FROM reportes")->fetch_assoc()['total'];
$reparaciones = $conexion->query("SELECT COUNT(*) as total FROM reparaciones")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">Bienvenido, Administrador <?= $_SESSION['nombre'] ?></h1>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card text-bg-primary text-center">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios</h5>
                        <p class="display-6"><?= $usuarios ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-success text-center">
                    <div class="card-body">
                        <h5 class="card-title">Sensores</h5>
                        <p class="display-6"><?= $sensores ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-warning text-center">
                    <div class="card-body">
                        <h5 class="card-title">Reportes</h5>
                        <p class="display-6"><?= $reportes ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-bg-danger text-center">
                    <div class="card-body">
                        <h5 class="card-title">Reparaciones</h5>
                        <p class="display-6"><?= $reparaciones ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="usuarios.php" class="btn btn-outline-primary">👥 Gestionar Usuarios</a>
            <a href="sensores.php" class="btn btn-outline-success">📡 Gestionar Sensores</a>
            <a href="reportes.php" class="btn btn-outline-warning">📊 Ver Reportes</a>
            <a href="reparaciones.php" class="btn btn-outline-danger">🔧 Ver Reparaciones</a>
            <a href="perfil.php" class="btn btn-outline-secondary">👤 Mi Perfil</a>
            <a href="../logout.php" class="btn btn-outline-dark">⛔ Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
