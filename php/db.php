<?php
$conn = new mysqli("localhost", "root", "", "proyecto_integrador");
date_default_timezone_set('America/Cancun');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
