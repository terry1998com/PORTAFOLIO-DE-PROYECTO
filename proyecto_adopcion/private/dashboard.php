<?php
// private/dashboard.php — Panel del Adoptante

// 1. Incluir el header, que a su vez incluye helpers/auth.php y db.php
require_once __DIR__ . '/../includes/header.php';

// 🔥 [CORRECCIÓN] Cambiado de requireAuth() a requireLogin()
// Esto verifica si el usuario está logueado y no espera argumentos.
requireLogin();

// Si la conexión no se inicializó en header.php, la inicializamos aquí:
if (!isset($pdo)) {
    // Es común inicializar la conexión en un archivo de configuración/funciones que se incluye.
    // Si $pdo no está disponible, es posible que falte un require_once en algún lugar.
    // Dejamos el die() como estaba, pero se recomienda incluir la conexión antes.
    die("Error: Conexión a base de datos no definida.");
}

$user_id = $_SESSION['user_id'] ?? null;
$nombre_usuario = $_SESSION['user_name'] ?? 'Adoptante';

// Solicitudes
$solicitudes = [];
if ($user_id) {
    $sql = "
        SELECT 
            s.id_solicitud,
            s.fecha_solicitud,
            s.estado AS estado_solicitud,
            m.nombre AS nombre_mascota,
            m.id_mascota,
            r.nombre_refugio
        FROM solicitudes_adopcion s
        JOIN mascotas m ON s.id_mascota = m.id_mascota
        JOIN refugios r ON m.id_refugio = r.id_refugio
        WHERE s.id_usuario = ?
        ORDER BY s.fecha_solicitud DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Colores por estado
$estado_clases = [
    'Pendiente' => 'background:#e7f0ff; color:#1d4ed8;',
    'Aprobada'  => 'background:#dcfce7; color:#166534;',
    'Rechazada' => 'background:#fee2e2; color:#b91c1c;',
];
?>

<style>
    .dashboard-container {
        padding: 35px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Bienvenida */
    .welcome-box {
        background: #f8f9fb;
        padding: 25px;
        border-radius: 12px;
        border-left: 4px solid #2563eb;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Secciones */
    .section-title {
        font-size: 22px;
        margin-bottom: 18px;
        font-weight: 600;
        color: #2d3748;
    }

    /* Solicitudes tarjetas */
    .solicitud-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }

    .solicitud-card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .solicitud-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
    }

    .badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    /* Links */
    .link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .link:hover {
        text-decoration: underline;
    }

    /* Acciones */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .action-card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .action-card:hover {
        transform: translateY(-3px);
    }
</style>

<div class="dashboard-container">

    <div class="welcome-box">
        <h2 style="margin:0; font-size:28px;">Hola, <?= htmlspecialchars($nombre_usuario) ?></h2>
        <p style="color:#555; margin-top:5px;">
            Este es tu panel personal. Aquí podrás revisar tus solicitudes de adopción.
        </p>
    </div>

    <div>
        <h3 class="section-title">Mis Solicitudes de Adopción</h3>

        <?php if (empty($solicitudes)): ?>
            <div class="solicitud-card" style="text-align:center; padding:35px;">
                <p style="font-size:18px; color:#666;">Aún no has realizado ninguna solicitud de adopción.</p>
                <a class="link" href="/proyecto_adopcion/public/catalogo.php">
                    Empieza a buscar un nuevo compañero
                </a>
            </div>
        <?php else: ?>
            <div class="solicitud-grid">
                <?php foreach ($solicitudes as $s): ?>
                    <?php
                    $estado = $s['estado_solicitud'];
                    $style  = $estado_clases[$estado] ?? "background:#e5e7eb; color:#374151;";
                    try {
                        $fecha = new DateTime($s['fecha_solicitud']);
                        $fecha_formato = $fecha->format('d/m/Y');
                    } catch (Exception $e) {
                        $fecha_formato = "Desconocida";
                    }
                    ?>
                    <div class="solicitud-card">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                            <div>
                                <h4 style="margin:0; font-size:20px;">
                                    Mascota:
                                    <a class="link" href="/proyecto_adopcion/public/mascota.php?id=<?= htmlspecialchars($s['id_mascota']) ?>">
                                        <?= htmlspecialchars($s['nombre_mascota']) ?>
                                    </a>
                                </h4>
                                <p style="margin:5px 0; color:#555;">
                                    Refugio: <strong><?= htmlspecialchars($s['nombre_refugio']) ?></strong>
                                </p>
                                <p style="margin:0; color:#666;">Fecha: <?= $fecha_formato ?></p>
                            </div>
                            <span class="badge" style="<?= $style ?>"><?= htmlspecialchars($estado) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <h3 class="section-title" style="margin-top:40px;">Otras Acciones</h3>
    <div class="action-grid">
        <div class="action-card">
            <h4 style="margin:0 0 10px; color:#059669;">Explorar Mascotas</h4>
            <p style="color:#666;">Conoce todas las mascotas disponibles para adoptar.</p>
            <a class="link" href="/proyecto_adopcion/public/catalogo.php">Ver catálogo</a>
        </div>
        <div class="action-card">
            <h4 style="margin:0 0 10px; color:#2563eb;">Proceso de Adopción</h4>
            <p style="color:#666;">¿Tienes dudas? Revisa los pasos a seguir para una adopción exitosa.</p>
            <a class="link" href="/proyecto_adopcion/public/proceso.php">Ver proceso</a>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>