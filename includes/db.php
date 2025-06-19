<?php
$host = 'srv1282.hstgr.io'; // Este es el host de Hostinger
$db   = 'u240362798_sistemaagua';
$user = 'u240362798_sistemaagua';
$pass = 'glcp.,?2A.'; // Tu contraseña

$conn = new mysqli($host, $user, $pass, $db);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
