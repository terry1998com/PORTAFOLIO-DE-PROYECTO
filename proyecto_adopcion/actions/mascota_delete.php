<?php
session_start();

// 🔥 Siempre cargar funciones y auth antes de usar requireRefugioAdmin
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../config/db.php';

requireRefugioAdmin(); // ← Ya no fallará

$id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$refugio_id = $_SESSION['refugio_id'];

if (!$id_mascota) {
    header("Location: ../admin/dashboard.php?msg=invalid_id");
    exit;
}

try {
    $pdo->prepare("DELETE FROM fotos_mascota WHERE id_mascota=?")->execute([$id_mascota]);
    $pdo->prepare("DELETE FROM mascotas_caracteristicas WHERE id_mascota=?")->execute([$id_mascota]);
    $pdo->prepare("DELETE FROM solicitudes_adopcion WHERE id_mascota=?")->execute([$id_mascota]);
    $pdo->prepare("DELETE FROM mascotas WHERE id_mascota=? AND id_refugio=?")
        ->execute([$id_mascota, $refugio_id]);

    header("Location: ../admin/dashboard.php?msg=success");
    exit;

} catch (PDOException $e) {
    error_log("Error al eliminar mascota: " . $e->getMessage());
    header("Location: ../admin/dashboard.php?msg=db_delete_failed");
    exit;
}
