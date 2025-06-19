<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "u240362798_sistemaagua";
$pass = "glcp.,?2A."; // <-- ya me diste esta clave
$db = "u240362798_sistemaagua";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Problemas en la conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
