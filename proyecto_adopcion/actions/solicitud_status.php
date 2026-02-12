<?php
// Archivo: actions/solicitud_status.php

include '../helpers/auth.php';
requireRefugioAdmin();
require_once '../config/db.php';

$id_solicitud = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$refugio_id = $_SESSION['refugio_id'];

if (!$id_solicitud || !in_array($status, ['Aprobada', 'Rechazada'])) {
    header('Location: ../admin/solicitudes.php?error=datos_invalidos');
    exit;
}

try {
    // Verificar que la solicitud pertenece a una mascota de este refugio
    $checkSql = "SELECT s.id_solicitud 
                 FROM solicitudes_adopcion s
                 JOIN mascotas m ON s.id_mascota = m.id_mascota
                 WHERE s.id_solicitud = ? AND m.id_refugio = ?";
    $stmt = $pdo->prepare($checkSql);
    $stmt->execute([$id_solicitud, $refugio_id]);
    
    if ($stmt->fetch()) {
        // Actualizar estado
        $updateSql = "UPDATE solicitudes_adopcion SET estado = ? WHERE id_solicitud = ?";
        $pdo->prepare($updateSql)->execute([$status, $id_solicitud]);
        
        header('Location: ../admin/solicitudes.php?msg=status_actualizado');
    } else {
        header('Location: ../admin/solicitudes.php?error=acceso_denegado');
    }

} catch (PDOException $e) {
    header('Location: ../admin/solicitudes.php?error=db_error');
}
?>