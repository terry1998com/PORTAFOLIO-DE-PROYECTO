<?php
// public/voluntariado.php - Diseño Mejorado

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL
$BASE_URL = "/proyecto_adopcion";

// Validar que el usuario esté logueado (opcional, pero recomendado)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Debes iniciar sesión para postularte como voluntario.";
    header("Location: $BASE_URL/public/login.php");
    exit;
}

// Si el usuario pertenece a un refugio, entonces tendrá esto:
$id_refugio = $_SESSION['refugio_id'] ?? null;

// 🔥🔥🔥 LÓGICA DE BLOQUEO CORREGIDA 🔥🔥🔥
require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'] ?? null;

// Solo ejecutamos la verificación si tenemos el ID de usuario
if ($user_id) {
    try {
        global $pdo;
        // Buscamos solo solicitudes que estén PENDIENTES. 
        // Si ya está Aprobada o Rechazada, permitimos una nueva.
        $sql = "SELECT estado FROM voluntarios WHERE id_usuario = ? AND estado = 'Pendiente'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $solicitud_pendiente = $stmt->fetchColumn();

        if ($solicitud_pendiente) {
            $_SESSION['warning'] = "Ya tienes una solicitud de voluntariado pendiente. Por favor, espera la respuesta del administrador.";
            header("Location: {$BASE_URL}/private/mi_voluntariado.php");
            exit;
        }
    } catch (PDOException $e) {
        // En caso de error de BD, simplemente permitimos al usuario enviar
        error_log("Error checking volunteer status: " . $e->getMessage());
    }
}
// 🔥🔥🔥 FIN LÓGICA DE BLOQUEO CORREGIDA 🔥🔥🔥

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postúlate como Voluntario - AdoptaAmigo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Definición de colores amigables (ej: verde menta, naranja suave, azul cielo) */
        :root {
            --color-primary-soft: #28a745; /* Verde Adopción */
            --color-secondary-soft: #ffc107;
            --color-bg-light: #f8f9fa;
        }

        body {
            background-color: var(--color-bg-light);
        }

        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            font-weight: 700;
        }

        .form-check-input:checked {
            background-color: var(--color-primary-soft);
            border-color: var(--color-primary-soft);
        }

        .btn-custom {
            background-color: var(--color-primary-soft);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #218838;
            color: white;
        }

        /* Estilo para los checkboxes como etiquetas de botón */
        .disponibilidad-label {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: normal;
        }

        .form-check-input:checked + .disponibilidad-label {
            background-color: var(--color-primary-soft);
            color: white;
            border-color: var(--color-primary-soft);
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
        }

        /* Ocultar el input checkbox original */
        .form-check-input {
            position: absolute;
            opacity: 0;
        }
    </style>
</head>

<body>
    
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="form-container">
                <h1 class="text-center mb-4">Formulario de Postulación a Voluntariado</h1>
                <p class="text-center text-muted mb-4">¡Gracias por querer ayudar a nuestros animales! Llena el formulario y nos pondremos en contacto contigo.</p>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= $BASE_URL ?>/actions/voluntariado_save.php" method="POST">
                    
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id'] ?? '' ?>">
                    <input type="hidden" name="id_refugio" value="<?= $id_refugio ?? '' ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Disponibilidad (Selecciona al menos una)</label>
                        <div class="d-flex flex-wrap">
                            <?php 
                                $disponibilidades = ['Mañanas (L-V)', 'Tardes (L-V)', 'Fin de semana', 'Días festivos'];
                                foreach ($disponibilidades as $index => $disp): 
                                    $id_input = 'disp' . $index;
                            ?>
                                <input class="form-check-input" type="checkbox" name="disponibilidad[]" value="<?= htmlspecialchars($disp) ?>" id="<?= $id_input ?>">
                                <label class="disponibilidad-label" for="<?= $id_input ?>">
                                    <?= htmlspecialchars($disp) ?>
                                </label>
                            <?php endforeach; ?>
                            <input type="hidden" name="disponibilidad_check" data-error-message="Debes seleccionar al menos una opción de disponibilidad.">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="mensaje" class="form-label">Mensaje (Cuéntanos por qué quieres ser voluntario)</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom">Enviar Postulación</button>
                    </div>
                </form>

            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validar checkboxes
        document.querySelector('form').addEventListener('submit', function(e) {
            const checks = document.querySelectorAll('input[name="disponibilidad[]"]:checked');
            const hidden = document.querySelector('input[name="disponibilidad_check"]');

            if (checks.length === 0) {
                // El script detiene el envío si no hay checkboxes marcados
                e.preventDefault();
                hidden.setCustomValidity(hidden.getAttribute('data-error-message'));
                hidden.reportValidity();
            } else {
                // Permite el envío si hay al menos uno marcado
                hidden.setCustomValidity('');
            }
        });

        // Toggle visual de los labels de disponibilidad (para que se vean como botones al hacer clic)
        document.querySelectorAll('.disponibilidad-label').forEach(label => {
            label.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('for'));
                if (input && input.type === 'checkbox') {
                    // La lógica del checkbox se maneja automáticamente
                    // Se agrega el evento 'change' para recalcular el estado visual
                }
            });
        });
    </script>

</body>

</html>