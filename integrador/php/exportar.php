<?php
require_once "db.php";

// Librerías necesarias
require_once "../lib/PHPStreadSheet/vendor/autoload.php";
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/PHPMailer.php";
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/SMTP.php";
require_once "../lib/PHPMailer/phpmailer/phpmailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fecha = $_POST['fecha'] ?? date("Y-m-d");
$correoDestino = $_POST['correo'] ?? null;


$sql = "
    SELECT u.nombre_usuario, u.apellido_paterno_usuario, u.apellido_materno_usuario,
           u.matricula_usuario, c.carrera_usuario,
           r.hora_entrada, r.hora_salida
    FROM registros r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN carreras c ON u.id_carrera = c.id_carrera
    WHERE r.fecha = ?
    ORDER BY r.hora_entrada ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();
$resultado = $stmt->get_result();

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Registros $fecha");

// Encabezados
$sheet->fromArray([
    ['Nombre', 'Apellido Paterno', 'Apellido Materno', 'Matrícula', 'Carrera', 'Hora Entrada', 'Hora Salida']
], NULL, 'A1');

// Datos
$fila = 2;
while ($row = $resultado->fetch_assoc()) {
    $sheet->fromArray([
        $row['nombre_usuario'],
        $row['apellido_paterno_usuario'],
        $row['apellido_materno_usuario'],
        $row['matricula_usuario'],
        $row['carrera_usuario'] ?? '-', 
        $row['hora_entrada'] ?? '-',
        $row['hora_salida'] ?? '-'
    ], NULL, 'A' . $fila);
    $fila++;
}

// Guardar archivo temporalmente
$nombreArchivo = "registros_" . $fecha . ".xlsx";
$rutaArchivo = __DIR__ . "/../temp/" . $nombreArchivo;
$writer = new Xlsx($spreadsheet);
$writer->save($rutaArchivo);

$stmt->close();
$conn->close();

// Enviar por correo o descargar
if ($correoDestino) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'perezgarciaangel2220@gmail.com';
        $mail->Password = 'faiobtuivlyfnlsl'; // Asegúrate de proteger esta contraseña
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('perezgarciaangel2220@gmail.com', 'Biblioteca UPQROO');
        $mail->addAddress($correoDestino);
        $mail->isHTML(true);
        $mail->Subject = "Registros del día $fecha";
        $mail->Body = "Adjunto se encuentra el archivo Excel con los registros de la biblioteca.";

        $mail->addAttachment($rutaArchivo, $nombreArchivo);
        $mail->send(); 
    } catch (Exception $e) {
        echo "Error al enviar correo: {$mail->ErrorInfo}";
    }
} else {
    // Descargar directamente
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=$nombreArchivo");
    readfile($rutaArchivo);
}

exit();
