<?php
require 'db.php';

$resultado = $conexion->query("
    SELECT r.fecha, u.nombre, u.matricula, r.hora_entrada, r.hora_salida, r.tipo
    FROM registros r
    JOIN usuarios u ON r.usuario_id = u.id
    ORDER BY r.fecha DESC, r.hora_entrada DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Registros</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        button { padding: 10px 20px; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>📚 Registros de Biblioteca</h1>
    <form method="POST" action="exportar_excel.php">
        <button type="submit">📤 Exportar a Excel</button>
    </form>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Nombre</th>
            <th>Matrícula</th>
            <th>Hora de Entrada</th>
            <th>Hora de Salida</th>
            <th>Tipo</th>
        </tr>
        <?php while($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['nombre'] ?></td>
                <td><?= $fila['matricula'] ?></td>
                <td><?= $fila['hora_entrada'] ?? '-' ?></td>
                <td><?= $fila['hora_salida'] ?? '-' ?></td>
                <td><?= ucfirst($fila['tipo'] ?? '-') ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
