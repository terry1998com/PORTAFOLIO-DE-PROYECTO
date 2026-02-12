<?php
// ==========================================
// helpers/auth.php
// Manejo de autenticación y roles
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar funciones generales (requireAuth, requireLogin, sanitize, etc.)
require_once __DIR__ . '/../includes/functions.php';

/* ============================================================
   FUNCIÓN isLoggedIn()
   - Retorna true si el usuario tiene sesión activa
   ============================================================ */
if (!function_exists('isLoggedIn')) {

    function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

/* ============================================================
   FUNCIÓN getCurrentRole()
   - Obtiene el ID del rol del usuario (rol_id o role_id)
   ============================================================ */
if (!function_exists('getCurrentRole')) {

    function getCurrentRole(): int
    {
        return $_SESSION['rol_id'] ?? $_SESSION['role_id'] ?? 0;
    }
}

/* ============================================================
   FUNCIÓN requireLogin()
   - Fuerza al usuario a iniciar sesión
   ============================================================ */
if (!function_exists('requireLogin')) {

    function requireLogin(): void
    {
        if (!isLoggedIn()) {
            $BASE = "/proyecto_adopcion";
            $_SESSION['error'] = "Debes iniciar sesión para continuar.";
            header("Location: {$BASE}/public/login.php");
            exit;
        }
    }
}

/* ============================================================
   FUNCIÓN requireAdmin()
   - Solo Admin Global (rol 4)
   ============================================================ */
if (!function_exists('requireAdmin')) {

    function requireAdmin(): void
    {
        // Usa el sistema central de autorizaciones
        requireAuth([4]);
    }
}

/* ============================================================
   FUNCIÓN requireRefugioAdmin()
   - Solo Admin Refugio (rol 5)
   - Además requiere tener refugio asignado
   ============================================================ */
if (!function_exists('requireRefugioAdmin')) {

    function requireRefugioAdmin(): void
    {
        // Verifica rol permitido
        requireAuth([5]);

        // Verifica refugio asignado
        if (!isset($_SESSION['refugio_id'])) {
            $BASE = "/proyecto_adopcion";
            header("Location: {$BASE}/public/login.php?error=no_refugio_asignado");
            exit;
        }
    }
}
