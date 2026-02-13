<?php
session_start();
require_once './db.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Buscar usuario en la base de datos
$sql = "SELECT * FROM users WHERE nombre = ? AND contraseña = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    // Redirigir según el rol
    if ($usuario['rol'] === 'admin') {
        header("Location: ../menu_administrador.php");
    } else {
        header("Location: ../menu_usuario.php");
    }
} else {
    $_SESSION['msg'] = "Usuario o contraseña incorrectos.";
    header("Location: ../index.php");
}
exit();

$stmt->close();
$conn->close();
