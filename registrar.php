<?php
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['matricula'])) {
    echo json_encode(["status" => "error", "message" => "QR inválido o vacío"]);
    exit;
}

$matricula = $data['matricula'];
$fecha = date("Y-m-d");
$hora = date("H:i:s");

// 1. Verificar si existe el usuario y obtener su id_usuario
$sql = "SELECT id_usuario FROM usuarios WHERE matricula_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insertar usuario nuevo
    $insertUser = $conn->prepare("INSERT INTO usuarios (nombre_usuario, apellido_paterno_usuario, apellido_materno_usuario, rol_usuario, matricula_usuario, carrera_usuario) VALUES (?, ?, ?, ?, ?, ?)");
    $insertUser->bind_param(
        "ssssss",
        $data['nombre'],
        $data['apellido_paterno'],
        $data['apellido_materno'],
        $data['rol'],
        $matricula,
        $data['carrera']
    );
    $insertUser->execute();
    $id_usuario = $insertUser->insert_id;
} else {
    $row = $result->fetch_assoc();
    $id_usuario = $row['id_usuario'];
}

// 2. Buscar el último registro del usuario para hoy (por hora_entrada descendente)
$sqlReg = "SELECT * FROM registros WHERE id_usuario = ? AND fecha = ? ORDER BY hora_entrada DESC LIMIT 1";
$stmtReg = $conn->prepare($sqlReg);
$stmtReg->bind_param("is", $id_usuario, $fecha);
$stmtReg->execute();
$resultReg = $stmtReg->get_result();

if ($fila = $resultReg->fetch_assoc()) {
    if (is_null($fila['hora_salida'])) {
        // Registrar salida en el registro abierto
        $update = $conn->prepare("UPDATE registros SET hora_salida = ? WHERE id_registro = ?");
        $update->bind_param("si", $hora, $fila['id_registro']);
        $update->execute();
        echo json_encode(["status" => "ok", "tipo" => "salida"]);
    } else {
        // Crear nuevo registro de entrada porque el anterior ya tiene salida
        $insertReg = $conn->prepare("INSERT INTO registros (id_usuario, fecha, hora_entrada) VALUES (?, ?, ?)");
        $insertReg->bind_param("iss", $id_usuario, $fecha, $hora);
        $insertReg->execute();
        echo json_encode(["status" => "ok", "tipo" => "entrada"]);
    }
} else {
    // No hay registros hoy, crear nuevo registro de entrada
    $insertReg = $conn->prepare("INSERT INTO registros (id_usuario, fecha, hora_entrada) VALUES (?, ?, ?)");
    $insertReg->bind_param("iss", $id_usuario, $fecha, $hora);
    $insertReg->execute();
    echo json_encode(["status" => "ok", "tipo" => "entrada"]);
}
?>
