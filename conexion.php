<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión real para Hostinger
$host = "localhost";
$user = "u240362798_sistemaagua"; // Este es tu usuario exacto
$pass = "glcp.,?2A.";     // ⚠️ Cambia esto por la contraseña correcta
$db = "u240362798_sistemaagua";   // Este es el nombre exacto de tu BD

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Problemas en la conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
