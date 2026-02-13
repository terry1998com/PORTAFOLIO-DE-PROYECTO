<?php
require_once "./php/db.php";
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    if ($_GET['ajax'] ?? '' === 'tabla') {
        http_response_code(403);
        echo "Sesión expirada";
    } else {
        header("Location: ./index.php");
    }
    exit();
}

$fechaSeleccionada = $_POST['fecha'] ?? $_GET['fecha'] ?? date("Y-m-d");

function obtenerRegistros($conn, $fecha)
{
    $sql = "
        SELECT u.nombre_usuario, u.apellido_paterno_usuario, u.apellido_materno_usuario, 
               u.matricula_usuario, c.carrera_usuario,
               r.hora_entrada, r.hora_salida, r.fecha
        FROM registros r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        LEFT JOIN carreras c ON u.id_carrera = c.id_carrera
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
                <td>" . htmlspecialchars($fila['nombre_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['apellido_paterno_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['apellido_materno_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['matricula_usuario']) . "</td>
                <td>" . htmlspecialchars($fila['carrera_usuario']) .  "</td> 
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
</head>

<body>
    <nav class="navbar" id="navbar">
        <div class="logo">
            <img src="./images/logo upqroo v2.png" alt="Logo de Mi Sitio">
        </div>
        <ul class="nav-links">
            <li>
                <button class="nav-links-buttons" onclick="location.href='./form_qr.php'">Crear QR de Estudiante</button>
            </li>
            <li>
                <form style="padding: 0;" action="./php/logout.php" method="post"><button class="nav-links-buttons" type="submit">Cerrar Sesión</button></form>
            </li>
        </ul>
    </nav>
    <h1 style="padding-top: 110px;">Historial de Entradas-Salidas de la Biblioteca</h1>

    <div class="option-container">
        <div class="option">
            <form method="POST" id="form-fecha">
                <label class="label-fecha">Seleccionar fecha:
                    <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fechaSeleccionada) ?>">
                </label>
            </form>
        </div>
        <div class="option">
            <form method="POST" id="form-exportar">
                <input type="hidden" name="fecha" id="fecha-exportar">
                <input type="hidden" name="correo" id="correo-input">
                <button type="button" id="btn-exportar">Obtener archivo Excel</button>
            </form>
        </div>
    </div>
    <table border="1">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Matricula</th>
                <th>Carrera</th>
                <th>Hora de Entrada</th>
                <th>Hora de Salida</th>
            </tr>
        </thead>
        <tbody id="tabla-registros">
            <?php
            $resultado = obtenerRegistros($conn, $fechaSeleccionada);
            while ($fila = $resultado->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($fila['fecha']) ?></td>
                    <td><?= htmlspecialchars($fila['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['apellido_paterno_usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['apellido_materno_usuario']) ?></td> 
                    <td><?= htmlspecialchars($fila['matricula_usuario']) ?></td>
                    <td><?= htmlspecialchars($fila['hora_entrada'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($fila['hora_salida'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <script src="./lib/sweetalert2.all.min.js"></script>
    <script src="./js/admin_tabla.js"></script>
    <script src="./js/animacion_navbar.js"></script>
</body>

</html>