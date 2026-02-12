<?php
// =============================================
// Archivo: private/solicitar_adopcion.php
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../helpers/auth.php';

// 1. Verificar login
requireLogin();

// Usuario actual
$id_usuario = $_SESSION['user_id'];
$user_name  = $_SESSION['user_name'] ?? 'Usuario';

// 2. Obtener ID de la mascota
$id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_mascota) {
    include __DIR__ . '/../includes/header.php';
    echo "<div class='container alert alert-danger mt-4'>Error: No se especificó la mascota.</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// 3. Consultar datos de la mascota
$stmt = $pdo->prepare("SELECT nombre FROM mascotas WHERE id_mascota = ?");
$stmt->execute([$id_mascota]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mascota) {
    include __DIR__ . '/../includes/header.php';
    echo "<div class='container alert alert-danger mt-4'>Mascota no encontrada.</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// 4. Procesar envío del formulario
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $telefono  = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $motivo    = trim($_POST['motivo'] ?? '');

    if ($telefono && $direccion && $motivo) {

        try {
            $sql = "INSERT INTO solicitudes_adopcion 
                    (id_usuario, id_mascota, fecha_solicitud, estado, telefono_contacto, direccion, motivo)
                    VALUES (?, ?, NOW(), 'Pendiente', ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario, $id_mascota, $telefono, $direccion, $motivo]);

            echo "<script>
                    alert('¡Solicitud enviada con éxito! El refugio te contactará pronto.');
                    window.location.href = '../public/catalogo.php';
                  </script>";
            exit;

        } catch (PDOException $e) {
            $error = "Error al enviar la solicitud: " . $e->getMessage();
        }

    } else {
        $error = "Por favor completa todos los campos obligatorios.";
    }
}

// ==========================
// MOSTRAR FORMULARIO
// ==========================
include __DIR__ . '/../includes/header.php';
?>

<style>
    .adopcion-card {
        background: #ffffff;
        padding: 35px;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .adopcion-card h2 {
        font-weight: 700;
        font-size: 26px;
        color: #ff7f32;
        text-align: center;
        margin-bottom: 5px;
    }

    .adopcion-card p {
        text-align: center;
        margin-bottom: 20px;
        color: #555;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #444;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 11px;
        border-radius: 8px;
        border: 1px solid #ccc;
        background: #fafafa;
        transition: 0.2s;
        font-size: 15px;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        background: #fff;
        border-color: #ff9900;
        box-shadow: 0 0 6px rgba(255,153,0,0.3);
        outline: none;
    }

    .btn-submit {
        background: linear-gradient(45deg, #ff9933, #ff7700);
        padding: 14px;
        border: none;
        color: white;
        font-weight: bold;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.2s;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }

    .btn-submit:hover {
        background: linear-gradient(45deg, #ff8800, #ff5500);
        transform: translateY(-2px);
    }
</style>


<div class="container" style="padding: 50px; max-width: 650px; margin: 0 auto;">

    <div class="adopcion-card">

        <h2>Solicitud de Adopción</h2>
        <p>Estás a un paso de darle un hogar a <strong><?= htmlspecialchars($mascota['nombre']) ?></strong> 🐾</p>

        <hr style="margin: 20px 0;">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Tu Nombre (Usuario):</label>
                <input type="text"
                       value="<?= htmlspecialchars($user_name) ?>"
                       disabled
                       style="background:#eee; font-weight:bold;">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono de Contacto *</label>
                <input type="text" name="telefono" required placeholder="Ej: 55 1234 5678">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección / Ciudad *</label>
                <input type="text" name="direccion" required placeholder="Ej: Ciudad de México, Col. Centro">
            </div>

            <div class="form-group">
                <label for="motivo">¿Por qué quieres adoptar a <?= htmlspecialchars($mascota['nombre']) ?>? *</label>
                <textarea name="motivo" rows="4" required placeholder="Cuéntanos sobre ti y tu hogar..."></textarea>
            </div>

            <button type="submit" class="btn-submit" style="width:100%; margin-top: 10px;">
                Enviar Solicitud
            </button>

            <a href="../public/catalogo.php"
               style="display:block; text-align:center; margin-top:15px; color:#666;">
                Cancelar
            </a>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
