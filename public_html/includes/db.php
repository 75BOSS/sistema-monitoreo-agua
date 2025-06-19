<?php
$host = "localhost";
$usuario = "u240362798_sistemaagua";
$contrasena = "glcp,.?2A.";
$base_datos = "u240362798_sistemaagua";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
