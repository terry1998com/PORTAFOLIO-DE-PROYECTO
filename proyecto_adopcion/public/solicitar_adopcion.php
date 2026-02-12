<?php
// Archivo: private/solicitar_adopcion.php (asumo que está en 'private')

// 1. LÓGICA Y REDIRECCIONES CRÍTICAS (DEBEN IR AL PRINCIPIO)
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../helpers/auth.php'; // Asegúrate de tener este include si 'isLoggedIn' está aquí

// Verificar Login: Esta debe ser la primera acción que puede usar 'header()'
if (!isLoggedIn()) {
    // La URL base es necesaria si la redirección debe ser absoluta
    $BASE_URL = "/proyecto_adopcion";
    header('Location: ' . $BASE_URL . '/public/login.php?msg=Debes iniciar sesión para adoptar');
    exit;
}

// 2. LÓGICA DE DATOS
$id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$id_usuario = $_SESSION['user_id'];

// Si no hay ID de mascota, se detiene la ejecución pero después de cargar el header para mostrar el error.
// Si no hay ID de mascota, detenemos la ejecución y ya podemos incluir el header de forma segura.
if (!$id_mascota) {
    include '../includes/header.php'; // Ahora es seguro incluir el header
    echo "<div class='container alert alert-danger' style='margin-top: 50px;'>Error: No se especificó la mascota.</div>";
    include '../includes/footer.php';
    exit;
}

// Consultar datos básicos de la mascota
$stmt = $pdo->prepare("SELECT nombre FROM mascotas WHERE id_mascota = ?");
$stmt->execute([$id_mascota]);
$mascota = $stmt->fetch();

if (!$mascota) {
    include '../includes/header.php'; // Ahora es seguro incluir el header
    echo "<div class='container alert alert-danger' style='margin-top: 50px;'>Mascota no encontrada.</div>";
    include '../includes/footer.php';
    exit;
}

// 3. Procesar el Formulario (POST)
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');

    // Limitar longitudes para evitar envío excesivo de datos
    $telefono = substr($telefono, 0, 50);
    $direccion = substr($direccion, 0, 255);
    $motivo = substr($motivo, 0, 2000);

    if ($telefono !== '' && $motivo !== '') {
        try {
            // Asumiendo que tienes la tabla 'solicitudes_adopcion'
            $sql_insert = "INSERT INTO solicitudes_adopcion (id_usuario, id_mascota, fecha_solicitud, estado, telefono_contacto, direccion, motivo) 
                            VALUES (?, ?, NOW(), 'Pendiente', ?, ?, ?)";
            
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([$id_usuario, $id_mascota, $telefono, $direccion, $motivo]);

            echo "<script>
                    alert('¡Solicitud enviada con éxito! El refugio te contactará pronto.');
                    window.location.href = '../public/catalogo.php';
                  </script>";
            exit;

        } catch (PDOException $e) {
            error_log('Error al enviar solicitud de adopción: ' . $e->getMessage());
            $error = "Error al enviar solicitud, inténtalo más tarde.";
        }
    } else {
        $error = "Por favor completa todos los campos obligatorios.";
    }
}

// 4. INICIO DEL OUTPUT HTML (Incluimos el header una única vez después de toda la lógica crítica)
// Si llegamos a este punto, significa que la verificación de login pasó y no hubo redirección.
include '../includes/header.php'; 

?>

<div class="container" style="padding: 40px; max-width: 600px; margin: 0 auto;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: #ff9933;">Solicitud de Adopción</h2>
        <p style="text-align: center;">Estás a un paso de darle un hogar a <strong><?php echo htmlspecialchars($mascota['nombre']); ?></strong> ❤️</p>
        
        <hr>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            
            <div class="form-group">
                <label>Tu Nombre (Usuario):</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" class="form-control" disabled style="background: #eee;">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono de Contacto *:</label>
                <input type="text" name="telefono" required class="form-control" placeholder="Ej: 55 1234 5678">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección / Ciudad *:</label>
                <input type="text" name="direccion" required class="form-control" placeholder="Ej: Ciudad de México, Col. Centro">
            </div>

            <div class="form-group">
                <label for="motivo">¿Por qué quieres adoptar a <?php echo htmlspecialchars($mascota['nombre']); ?>? *</label>
                <textarea name="motivo" rows="4" required class="form-control" placeholder="Cuéntanos sobre ti y tu hogar..."></textarea>
            </div>

            <button type="submit" class="btn btn-warning btn-submit" style="width: 100%; margin-top: 15px;">Enviar Solicitud 🐾</button>
            
            <a href="../public/catalogo.php" style="display: block; text-align: center; margin-top: 15px; color: #666;">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>