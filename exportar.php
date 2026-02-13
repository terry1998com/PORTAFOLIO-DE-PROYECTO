<?php
require 'db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=registros_biblioteca.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Apellido Paterno</th>
        <th>Apellido Materno</th>
        <th>Rol</th>
        <th>Matrícula</th>
        <th>Hora de Entrada</th>
        <th>Hora de Salida</th>
      </tr>";

$resultado = $conn->query("
    SELECT r.fecha, u.nombre_usuario, u.apellido_paterno_usuario, u.apellido_materno_usuario,u.rol_usuario, u.matricula_usuario, r.hora_entrada, r.hora_salida
    FROM registros r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    ORDER BY r.fecha ASC
");

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$fila['fecha']}</td>
            <td>{$fila['nombre_usuario']}</td>
            <td>{$fila['apellido_paterno_usuario']}</td>
            <td>{$fila['apellido_materno_usuario']}</td>
            <td>{$fila['rol_usuario']}</td>
            <td>{$fila['matricula_usuario']}</td>
            <td>{$fila['hora_entrada']}</td>
            <td>{$fila['hora_salida']}</td>
          </tr>";
}
echo "</table>";
?>
