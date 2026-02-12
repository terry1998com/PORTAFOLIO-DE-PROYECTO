<?php
// ==============================
// includes/header.php
// ==============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================================================
// CONFIGURACIÓN DE RUTAS Y REQUIRES
// =======================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/functions.php';

// URL BASE
$BASE_URL = "/proyecto_adopcion";

// =======================================================
// FUNCIONES Y LÓGICA DE NAVEGACIÓN
// =======================================================

// Verifica si el usuario está logueado
if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
}

// Obtiene el rol actual del usuario
if (!function_exists('getCurrentRole')) {
    function getCurrentRole()
    {
        return $_SESSION['rol_id'] ?? $_SESSION['role_id'] ?? null;
    }
}

// Datos de sesión
$user_name = $_SESSION['user_name'] ?? 'Usuario';
$is_logged_in = isLoggedIn();
$current_role = getCurrentRole();
$is_admin = $is_logged_in && ($current_role == 4 || $current_role == 5);

// Mostrar enlaces según rol
$show_voluntariado_link = !$is_admin;
$show_my_voluntariado_dropdown = $is_logged_in && !$is_admin;

// URL Voluntariado
$voluntariado_url = $is_logged_in && $show_voluntariado_link
    ? "$BASE_URL/private/mi_voluntariado.php"
    : "$BASE_URL/public/voluntariado.php";

// URL Tips
$tips_url = $is_logged_in
    ? "$BASE_URL/private/tips.php"  // Usuarios logueados
    : "$BASE_URL/public/tips.php";  // Invitados

// Página actual para resaltar menú
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Función para marcar enlace activo
function isActive($page, $current_page)
{
    return ($page === $current_page) ? 'active' : '';
}

// Dropdown resaltar padre
$adoptar_pages = ['catalogo', 'proceso', 'preguntas'];
$nosotros_pages = ['nosotros', 'mision', 'prensa'];

$is_adoptar_active = in_array($current_page, $adoptar_pages) ? 'active' : '';
$is_nosotros_active = in_array($current_page, $nosotros_pages) ? 'active' : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopta un Amigo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">

    <style>
        /* ================= ESTILOS NAVBAR ================= */
        .navbar-custom {
            background-color: #3a1f0b;
            padding: 0.8rem 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 12px 12px;
        }

        .navbar-custom .navbar-brand {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .navbar-custom .navbar-brand:hover {
            color: #f59e0b;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            transition: color 0.2s;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: #f59e0b;
        }

        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .navbar-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-outline-custom {
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 0.5rem 1.2rem;
            font-size: 0.95rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-outline-custom:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #fff;
            color: #fff;
        }

        .btn-donar {
            background-color: #f59e0b;
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            border: none;
            transition: transform 0.2s;
        }

        .btn-donar:hover {
            background-color: #d97706;
            transform: scale(1.05);
            color: #fff;
        }

        .btn-registro {
            background-color: #f59e0b;
            color: #3a1f0b;
            font-weight: 700;
            padding: 0.5rem 1.2rem;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-registro:hover {
            background-color: #d97706;
            color: #fff;
        }

        .user-dropdown-toggle {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: 0.3s;
        }

        .user-dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .vertical-divider {
            height: 24px;
            width: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 0 1rem;
        }

        @media (max-width:991px) {
            .navbar-actions {
                width: 100%;
                margin-top: 1rem;
            }

            .vertical-divider {
                display: none;
            }

            .user-dropdown-toggle {
                width: 100%;
                justify-content: flex-start;
                background-color: rgba(0, 0, 0, 0.15);
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="<?= $BASE_URL ?>/public/index.php"><i class="bi bi-paw-fill"></i> AdoptaAmigo</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item"><a class="nav-link <?= isActive('index', $current_page) ?>" href="<?= $BASE_URL ?>/public/index.php">Inicio</a></li>

                        <!-- Adoptar -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $is_adoptar_active ?>"
                                href="<?= $BASE_URL ?>/public/catalogo.php"
                                id="dropdownAdoptar" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Adoptar
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownAdoptar">
                                <li><a class="dropdown-item <?= isActive('catalogo', $current_page) ?>" href="<?= $BASE_URL ?>/public/catalogo.php">Mascotas Disponibles</a></li>
                                <li><a class="dropdown-item <?= isActive('proceso', $current_page) ?>" href="<?= $BASE_URL ?>/public/proceso.php">Proceso de Adopción</a></li>
                                <li><a class="dropdown-item <?= isActive('preguntas', $current_page) ?>" href="<?= $BASE_URL ?>/public/preguntas.php">Preguntas Frecuentes</a></li>
                            </ul>
                        </li>

                        <!-- Nosotros -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $is_nosotros_active ?>"
                                href="<?= $BASE_URL ?>/public/nosotros.php"
                                id="dropdownNosotros" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Nosotros
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownNosotros">
                                <li><a class="dropdown-item <?= isActive('nosotros', $current_page) ?>" href="<?= $BASE_URL ?>/public/nosotros.php">Sobre Nosotros</a></li>
                                <li><a class="dropdown-item <?= isActive('mision', $current_page) ?>" href="<?= $BASE_URL ?>/public/mision.php">Misión</a></li>
                                <li><a class="dropdown-item <?= isActive('prensa', $current_page) ?>" href="<?= $BASE_URL ?>/public/prensa.php">Prensa</a></li>
                            </ul>
                        </li>

                        <!-- Voluntariado -->
                        <?php if ($show_voluntariado_link): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= isActive('voluntariado', $current_page) ?> <?= isActive('mi_voluntariado', $current_page) ?>" href="<?= $voluntariado_url ?>">Voluntariado</a>
                            </li>
                        <?php endif; ?>

                        <!-- Tips -->
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('tips', $current_page) ?>" href="<?= $tips_url ?>">Tips</a>
                        </li>
                    </ul>

                    <div class="vertical-divider d-none d-lg-block"></div>

                    <!-- Acciones -->
                    <div class="navbar-actions">
                        <?php if ($is_logged_in): ?>
                            <div class="dropdown ms-lg-2">
                                <button class="btn user-dropdown-toggle dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle fs-5"></i> <?= htmlspecialchars($user_name) ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <h6 class="dropdown-header">Mi Cuenta</h6>
                                    <?php if ($is_admin): ?>
                                        <li><a class="dropdown-item" href="<?= $BASE_URL ?>/admin/dashboard.php"><i class="bi bi-speedometer2"></i> Panel Admin</a></li>
                                        <?php if ($current_role == 5): ?>
                                            <li><a class="dropdown-item" href="<?= $BASE_URL ?>/admin/voluntarios.php"><i class="bi bi-people-fill"></i> Gestión Voluntarios</a></li>
                                        <?php endif; ?>
                                        <?php if ($current_role == 4): ?>
                                            <li><a class="dropdown-item" href="<?= $BASE_URL ?>/admin/dashboard_global.php"><i class="bi bi-globe"></i> Panel Global</a></li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="<?= $BASE_URL ?>/private/dashboard.php"><i class="bi bi-file-earmark-check"></i> Mis Solicitudes</a></li>
                                        <?php if ($show_my_voluntariado_dropdown): ?>
                                            <li><a class="dropdown-item fw-bold text-primary" href="<?= $BASE_URL ?>/private/mi_voluntariado.php"><i class="bi bi-person-workspace"></i> Mi Voluntariado</a></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="<?= $BASE_URL ?>/actions/auth.php?action=logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="d-grid gap-2 d-lg-flex ms-lg-2">
                                <a href="<?= $BASE_URL ?>/public/login.php" class="btn btn-outline-custom">Entrar</a>
                                <a href="<?= $BASE_URL ?>/public/registro.php" class="btn btn-registro">Registrarse</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para dropdowns clicables y desplegables correctamente -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.nav-item.dropdown > .nav-link').forEach(function(dropdownLink) {
                dropdownLink.addEventListener('click', function(e) {
                    const parent = this.parentElement;
                    const menu = parent.querySelector('.dropdown-menu');
                    const isShown = menu.classList.contains('show');

                    if (!isShown) {
                        e.preventDefault(); // evita la navegación si el dropdown está cerrado
                    }
                });
            });
        });
    </script>

</body>

</html>