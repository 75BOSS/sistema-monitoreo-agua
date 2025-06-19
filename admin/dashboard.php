<?php require_once "../includes/header.php"; ?>
<h2>Dashboard del Administrador</h2>
<p>Bienvenido, <?= $_SESSION["nombre"] ?>.</p>
<div class="row">
    <div class="col-md-3"><a href="usuarios.php" class="btn btn-primary w-100">Usuarios</a></div>
    <div class="col-md-3"><a href="sensores.php" class="btn btn-primary w-100">Sensores</a></div>
    <div class="col-md-3"><a href="reportes.php" class="btn btn-primary w-100">Reportes</a></div>
    <div class="col-md-3"><a href="alertas.php" class="btn btn-danger w-100">Alertas</a></div>
</div>
<?php require_once "../includes/footer.php"; ?>
