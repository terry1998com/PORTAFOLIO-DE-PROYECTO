<?php
// ===============================
// includes/functions.php
// Funciones generales del sistema
// ===============================

// Mantener sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$BASE_URL = "/proyecto_adopcion";

/* ============================================================
   FUNCIÓN requireAuth()
   - Restringe acceso según ROL
   - Usa requireAuth([roles permitidos])
   ============================================================ */
if (!function_exists('requireAuth')) {

    function requireAuth(array $allowed_roles): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $BASE_URL;

        if (empty($_SESSION['user_id'])) {
            $_SESSION['error'] = "Debes iniciar sesión para acceder a esta área.";
            header("Location: {$BASE_URL}/public/login.php");
            exit;
        }

        // Detecta rol
        $user_role_id = $_SESSION['rol_id'] ?? $_SESSION['role_id'] ?? 0;

        // Si no está permitido → redirige
        if (!in_array($user_role_id, $allowed_roles)) {
            $_SESSION['error'] = "Acceso denegado. No tienes permisos.";
            header("Location: {$BASE_URL}/private/dashboard.php?error=no_autorizado");
            exit;
        }
    }
}

/* ============================================================
   FUNCIÓN requireLogin()
   - Obliga a tener sesión activa
   ============================================================ */
if (!function_exists('requireLogin')) {

    function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $BASE_URL;

        if (empty($_SESSION['user_id'])) {
            header("Location: {$BASE_URL}/public/login.php?msg=Debes iniciar sesión");
            exit;
        }
    }
}

/* ============================================================
   FUNCIÓN sanitize()
   - Limpia entrada de texto para evitar XSS
   ============================================================ */
if (!function_exists('sanitize')) {

    function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}

/* ============================================================
   FUNCIÓN redirect()
   - Redirección segura
   ============================================================ */
if (!function_exists('redirect')) {

    function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }
}

/* ============================================================
   FUNCIÓN flashMessage()
   - Guarda mensajes flash en sesión
   ============================================================ */
if (!function_exists('flashMessage')) {

    function flashMessage(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }
}

/* ============================================================
   FUNCIÓN getFlash()
   - Obtiene y borra mensaje flash
   ============================================================ */
if (!function_exists('getFlash')) {

    function getFlash(string $key): ?string
    {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }

        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}
