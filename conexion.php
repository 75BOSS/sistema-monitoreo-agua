<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost"; // Cambia si en el panel dice otro host, como una IP
$user = "u240362798_sistemaagua";
$pass = "Lap2incasablanca1"; // Nueva contraseña
$db   = "u240362798_sistemaagua";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Problemas en la conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
