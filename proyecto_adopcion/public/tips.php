<?php
// ==============================
// tips.php - Página de Tips de Cuidado
// ==============================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Nota: session_start() se maneja en header.php
$BASE_URL = "/proyecto_adopcion";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize("Tips de Cuidado - AdoptaAmigo") ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= sanitize($BASE_URL) ?>/assets/css/style.css">

    <style>
        body {
            background-color: #fdf5f0;
        }

        h1 {
            color: #3a1f0b;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            color: #3a1f0b;
            font-weight: 600;
        }

        .card-text {
            color: #5a3d1b;
        }

        .icon-tip {
            font-size: 40px;
            color: #f59e0b;
        }

        .container-custom {
            max-width: 1000px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .card-vol {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 20px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <!-- Contenido principal -->
    <main class="container container-custom">
        <h1 class="text-center mb-5"><?= sanitize("Tips para el Cuidado de tus Mascotas") ?></h1>

        <div class="row g-4">
            <!-- Tip 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-3 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <span class="icon-tip me-3">&#128062;</span> <!-- 🐾 -->
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
                        <span class="icon-tip me-3">&#127958;</span> <!-- 🏃‍♂️ pero más mascota: 🐕 = &#128054; -->
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

            <!-- Footer -->
            <?php include __DIR__ . '/../includes/footer.php'; ?>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>