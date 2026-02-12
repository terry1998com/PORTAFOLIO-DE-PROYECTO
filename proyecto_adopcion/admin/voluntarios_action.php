<?php
// admin/voluntarios_action.php - CON SOLUCIÓN POR ID DE USUARIO ÚNICO (FINAL)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// ID ÚNICO DE TU ADMINISTRADOR (Asignado al 11, el más probable)
$ADMIN_ID_UNICO = 11; 

// Ruta y página de redirección
$BASE_URL = "/proyecto_adopcion"; 
// 🔥 [CORRECCIÓN] Cambiado a la página de la lista de voluntarios
$DEFAULT_REDIRECT_PAGE = "admin/voluntarios.php"; 


/**
 * Reemplazo de requireAdmin() para verificar SOLO el ID de usuario único.
 */
function requireAdminBypass()
{
    global $ADMIN_ID_UNICO;
    $base = "/proyecto_adopcion"; 
    
    $current_user_id = $_SESSION['user_id'] ?? null;

    // Si no está logueado O si el ID de usuario NO coincide con el administrador único
    if (empty($current_user_id) || $current_user_id != $ADMIN_ID_UNICO) { 
        
        $_SESSION['error'] = "Acceso denegado. Se requiere cuenta de administrador autorizada.";
        header("Location: {$base}/private/dashboard.php?error=no_autorizado"); 
        exit;
    }
}

// Ejecutar la verificación de seguridad por ID
requireAdminBypass();


// Obtener parámetros
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

// Validar datos
if (!$id || !in_array($action, ['aprobar', 'rechazar'])) {
    $_SESSION['error'] = "Acción no válida o ID faltante.";
    // 🔥 [Redirección corregida en la línea de abajo]
    header("Location: {$BASE_URL}/private/{$DEFAULT_REDIRECT_PAGE}");
    exit;
}

try {
    global $pdo;
    
    // Definir el nuevo estado
    $nuevo_estado = ($action == 'aprobar') ? 'Aprobado' : 'Rechazado';

    // Actualizar base de datos
    $stmt = $pdo->prepare("UPDATE voluntarios SET estado = :estado WHERE id = :id");
    $stmt->execute(['estado' => $nuevo_estado, 'id' => $id]);

    $_SESSION['success'] = "Solicitud actualizada a: " . $nuevo_estado;

} catch (PDOException $e) {
    error_log("Error al actualizar estado de voluntario: " . $e->getMessage());
    $_SESSION['error'] = "Error de base de datos al realizar la acción. Consulte los logs.";
}

// Redirección final: Volver a la página de la lista de voluntarios
header("Location: {$BASE_URL}/private/{$DEFAULT_REDIRECT_PAGE}");
exit;