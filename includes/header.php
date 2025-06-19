<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Monitoreo</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos personalizados si tienes -->
    <link rel="stylesheet" href="/assets/css/estilos.css">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#">Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/sensores.php">Sensores</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/reportes.php">Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/alertas.php">Alertas</a></li>
            </ul>
            <span class="navbar-text text-light">Administrador</span>
            <a href="/logout.php" class="btn btn-outline-light ms-3">Cerrar sesión</a>
        </div>
    </nav>
    <div class="container mt-4">
