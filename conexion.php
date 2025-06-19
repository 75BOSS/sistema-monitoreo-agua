<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DATOS ACTUALES DEL SISTEMA DE MONITOREO
$host = "localhost";
$user = "u240362798_sistemaagua";
$pass = "glcp,.?2A.";
$db   = "u240362798_sistemaagua";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Problemas en la conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
