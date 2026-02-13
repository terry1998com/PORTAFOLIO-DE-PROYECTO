<?php
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/PHPMailer.php";
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/SMTP.php";
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : 'Estudiante';
    $matricula = isset($_POST['matricula']) ? $_POST['matricula'] : 'Matricula';
    $email = $_POST['email'] ?? '';
    $filename = $_POST['filename'] ?? '';

    if (empty($email) || empty($filename)) {
        echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        exit;
    }

    $rutaImagen = "../temp/" . basename($filename);

    if (!file_exists($rutaImagen)) {
        echo json_encode(["status" => "error", "message" => "Archivo no encontrado"]);
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'perezgarciaangel2220@gmail.com';
        $mail->Password = 'faiobtuivlyfnlsl';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('perezgarciaangel2220@gmail.com', 'Biblioteca UPQROO');
        $mail->addAddress($email);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = "Codigo QR de matricula $matricula";
        $mail->Body = "
        <h2>Hola Administrador</h2>
        <p>Este codigo QR pertenece al alumno $nombre, con matricula $matricula.</p>
        <br>
        <p>Equipo de Biblioteca UPQROO</p>
        ";
        $mail->addAttachment($rutaImagen);

        $mail->send();
        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
    }
}
?>