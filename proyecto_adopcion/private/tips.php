<?php
// ==============================
// private/tips.php - Tips de Cuidado
// ==============================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario esté logueado y NO sea admin/refugio
$current_role = $_SESSION['rol_id'] ?? $_SESSION['role_id'] ?? null;
if (!isset($_SESSION['user_id']) || ($current_role == 4 || $current_role == 5)) {
    header("Location: ../public/tips.php");
    exit;
}

// Definir página actual para header
$current_page = 'tips';

// Incluir header
include __DIR__ . '/../includes/header.php';
?>

<main class="container container-custom">
    <h1 class="text-center mb-5"><?= sanitize("Tips para el Cuidado de tus Mascotas") ?></h1>

    <div class="row g-4">
        <!-- Tip 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#128062;</span>
                    <h5 class="card-title mb-0"><?= sanitize("Alimentación Balanceada") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Proporciona alimentos adecuados según la edad, tamaño y especie de tu mascota. Consulta con un veterinario si tienes dudas.") ?>
                </p>
            </div>
        </div>

        <!-- Tip 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#128054;</span> <!-- 🐕 -->
                    <h5 class="card-title mb-0"><?= sanitize("Ejercicio Diario") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Las mascotas necesitan ejercicio regular para mantenerse saludables y felices. Paseos, juegos y actividades son esenciales.") ?>
                </p>
            </div>
        </div>

        <!-- Tip 3 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#129657;</span> <!-- 🩺 -->
                    <h5 class="card-title mb-0"><?= sanitize("Visitas al Veterinario") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Mantén al día las vacunas y revisiones médicas. Un chequeo regular ayuda a prevenir enfermedades y detectar problemas a tiempo.") ?>
                </p>
            </div>
        </div>

        <!-- Tip 4 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#10084;&#65039;</span> <!-- ❤️ -->
                    <h5 class="card-title mb-0"><?= sanitize("Cariño y Atención") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Dedica tiempo a interactuar con tu mascota. El cariño, la atención y el juego fortalecen el vínculo y reducen el estrés.") ?>
                </p>
            </div>
        </div>

        <!-- Tip 5 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#128705;</span> <!-- 🛁 -->
                    <h5 class="card-title mb-0"><?= sanitize("Higiene") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Mantén limpia a tu mascota y su entorno. Baños, cepillados y limpieza del área de descanso son esenciales para su salud.") ?>
                </p>
            </div>
        </div>

        <!-- Tip 6 -->
        <div class="col-md-6 col-lg-4">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <span class="icon-tip me-3">&#127918;</span> <!-- 🎾 -->
                    <h5 class="card-title mb-0"><?= sanitize("Educación y Reglas") ?></h5>
                </div>
                <p class="card-text">
                    <?= sanitize("Enseña normas básicas y hábitos saludables. La educación ayuda a prevenir problemas de comportamiento y facilita la convivencia.") ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de ejemplo -->
    <div class="modal fade" id="modalExample" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-vol">
                <div class="modal-header">
                    <h5 class="modal-title"><?= sanitize("Información Extra") ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <?= sanitize("Aquí puedes agregar información adicional sobre cuidados de mascotas o voluntariado.") ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal"><?= sanitize("Cerrar") ?></button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Incluir footer
include __DIR__ . '/../includes/footer.php';
?>