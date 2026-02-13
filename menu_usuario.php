<?php
require_once "./php/db.php";
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: ./index.php");
    exit();
}

$fechaSeleccionada = $_POST['fecha'] ?? $_GET['fecha'] ?? date("Y-m-d");

function obtenerRegistros($conn, $fecha)
{
    $sql = "
        SELECT u.nombre_usuario, u.apellido_paterno_usuario, u.apellido_materno_usuario, 
               u.matricula_usuario, 
               u.id_carrera,               
               r.hora_entrada, r.hora_salida, r.fecha
        FROM registros r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.fecha = '$fecha'
        ORDER BY r.hora_entrada ASC
    ";
    return $conn->query($sql);
}

// Respuesta AJAX para actualizar tabla
if ($_GET['ajax'] ?? '' === 'tabla') {
    $resultado = obtenerRegistros($conn, $fechaSeleccionada);
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($fila['fecha']) . "</td>
                <td>" . htmlspecialchars($fila['nombre_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['apellido_paterno_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['apellido_materno_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['matricula_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['carrera_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['hora_entrada'] ?? '-') . "</td>
                <td>" . htmlspecialchars($fila['hora_salida'] ?? '-') . "</td>
              </tr>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro QR - Biblioteca</title>
    <link rel="stylesheet" href="./css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="./lib/html5-qrcode.min.js"></script>
</head>

<body>
    <nav class="navbar" id="navbar">
        <div class="logo">
            <img src="./images/logo upqroo v2.png" alt="Logo de Mi Sitio">
        </div>
        <ul class="nav-links">
            <li>
                <form style="padding: 0;" action="./php/logout.php" method="post"><button class="nav-links-buttons" type="submit">Cerrar Sesión</button></form>
            </li>
        </ul>
    </nav>
    <h1 style="padding-top: 110px;">Escanea el QR de tu credencial</h1>

    <div id="lector-wrapper" class="lector-inactivo">
        <div id="reader" style="width: 400px; height: 400px; display: none;"></div>
        <div class="toggle-lector-btn">
            <button id="btnToggleLector">Encender lector desde camara web</button>
            <div id="resultado" class="result-scan"></div>
        </div>
        <input id="scanner-input" type="text" autocomplete="off" style="opacity:0; position:absolute; z-index:-1;">
    </div>
    <script src="./js/escanear.js"></script>
    <script src="./js/animacion_navbar.js"></script>
</body>

</html>