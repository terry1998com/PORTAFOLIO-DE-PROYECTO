<?php
// actions/voluntariado_save.php
// Script actualizado para manejar el campo de disponibilidad (checkboxes) e IDs de usuario/refugio.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$BASE_URL = "/proyecto_adopcion";

// 1. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$BASE_URL}/public/voluntariado.php");
    exit;
}

// 2. Sanitizar y obtener datos
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// 🔥 [CORRECCIÓN] Captura de IDs desde los campos ocultos del formulario
$id_usuario = $_POST['id_usuario'] ?? null;
$id_refugio = $_POST['id_refugio'] ?? null; 

// Obtener el array de disponibilidad y convertirlo a JSON para un almacenamiento seguro
$disponibilidad_array = $_POST['disponibilidad'] ?? [];

// Convertir el array de disponibilidad a una cadena JSON para almacenarlo en una columna TEXT/VARCHAR
$disponibilidad_json = json_encode($disponibilidad_array);

// 3. Validaciones
if (empty($nombre) || empty($email) || empty($mensaje) || empty($disponibilidad_array)) {
    $_SESSION['error'] = "Todos los campos (incluida la disponibilidad) son obligatorios.";
    header("Location: {$BASE_URL}/public/voluntariado.php");
    exit;
}

// Validar que el ID de usuario exista (debe venir de la sesión)
if (empty($id_usuario)) {
    $_SESSION['error'] = "Error de sesión. No se pudo obtener el ID del usuario.";
    header("Location: {$BASE_URL}/public/voluntariado.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El correo electrónico no es válido.";
    header("Location: {$BASE_URL}/public/voluntariado.php");
    exit;
}

// 4. Insertar en BD
try {
    // 🔥 [CORRECCIÓN] Consulta ahora incluye id_usuario y id_refugio
    $stmt = $pdo->prepare("INSERT INTO voluntarios (nombre, email, mensaje, disponibilidad, id_usuario, id_refugio, fecha, estado) VALUES (:nombre, :email, :mensaje, :disponibilidad_json, :id_usuario, :id_refugio, NOW(), 'Pendiente')");
    
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mensaje', $mensaje);
    $stmt->bindParam(':disponibilidad_json', $disponibilidad_json);
    // 🔥 [NUEVOS PARÁMETROS] 
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':id_refugio', $id_refugio);
    
    $stmt->execute();

    $_SESSION['success'] = "¡Gracias por postularte! Te contactaremos pronto.";
    
    // Opcional: Enviar correo (usar PHPMailer en producción)
    $adminEmail = "admin@adoptaamigo.com";
    $subject = "Nuevo Voluntario: $nombre";
    $body = "Nombre: $nombre\nEmail: $email\nMensaje: $mensaje\nDisponibilidad: " . implode(', ', $disponibilidad_array);
    $headers = "From: no-reply@adoptaamigo.com";

    // @mail($adminEmail, $subject, $body, $headers); 
    
} catch (PDOException $e) {
    error_log("Error BD Voluntarios: " . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error interno. Intenta más tarde.";
}

header("Location: {$BASE_URL}/public/voluntariado.php");
exit;