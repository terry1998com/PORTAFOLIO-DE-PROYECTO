<?php
require_once "./php/db.php";
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ./index.php");
    exit();
}

$carreras = [];
$sql = "SELECT id_carrera, carrera_usuario FROM carreras ORDER BY carrera_usuario ASC";
$resultado = $conn->query($sql);
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $carreras[] = $fila;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear QR de Estudiante</title>
    <link rel="stylesheet" href="./css/qr_styles.css">
    <script src="./lib/qr-code-styling.js"></script>
</head>

<body>
    <nav class="navbar" id="navbar">
        <div class="logo">
            <img src="./images/logo upqroo v2.png" alt="Logo de Mi Sitio">
        </div>
        <ul class="nav-links">
            <li>
                <button class="nav-links-buttons" onclick="location.href='./menu_administrador.php'">Volver al Historial</button>
            </li>
            <li>
                <form style="padding: 0;" action="./php/logout.php" method="post"><button class="nav-links-buttons" type="submit">Cerrar Sesión</button></form>
            </li>
        </ul>
    </nav>

    <div class="main-container">
        <h2 class="title">Ingresar datos</h2>
        <p class="subtitle">Ingrese los datos del estudiantes para crear el QR</p>
        <form id="qr-form" method="POST">
            <div class="input-group">
                <input type="text" required id="nombre" name="name">
                <label for="name">Nombre</label>
            </div>
            <div class="input-group">
                <input type="text" required id="aPaterno" name="first-name">
                <label for="first-name">Apellido Paterno</label>
            </div>
            <div class="input-group">
                <input type="text" required id="aMaterno" name="last-name">
                <label for="first-name">Apellido Materno</label>
            </div>
            <div class="input-group">
                <input type="text" required id="matricula" name="matricula">
                <label for="matricula">Matrícula / Clave</label>
            </div>
            <div class="input-group">
                <label for="carrera">Carrera</label>
                <select id="carrera" name="id_carrera" required>
                    <option value="" disabled selected>Selecciona una carrera</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?= htmlspecialchars($carrera['id_carrera']) ?>">
                            <?= htmlspecialchars($carrera['carrera_usuario']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="create-btn">Generar QR</button>
        </form>
    </div>

    <script src="./lib/sweetalert2.all.min.js"></script>
    <script src="./js/generar_QR.js"></script>
    <script src="./js/animacion_navbar.js"></script>
</body>

</html>