<?php
// actions/api_mascota.php — MODAL ORGANIZADO Y BONITO

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// RUTA CORREGIDA: Se asegura de incluir el archivo db.php desde la carpeta config/
require_once __DIR__ . '/../config/db.php'; 

// Siempre regresamos HTML
header('Content-Type: text/html; charset=utf-8');

$mascota_id = $_GET['id'] ?? null;

if (!$mascota_id) {
    http_response_code(400);
    echo "<div class='p-5 text-center'>ID de mascota no proporcionado.</div>";
    exit;
}

try {
    // Aseguramos que la conexión esté disponible, si db.php la definió como global
    global $pdo; 
    
    // **TROUBLESHOOTING CRÍTICO**: Verifica que $pdo esté inicializado
    if (!isset($pdo) || !$pdo instanceof PDO) {
         throw new Exception("La conexión a la base de datos (\$pdo) no está disponible.");
    }

    // ================================
    // 1. Consultar datos de la mascota
    // ================================
    $sql = "
        SELECT m.*, e.nombre_especie, r.nombre_refugio, ra.nombre_raza
        FROM mascotas m
        JOIN especies e ON m.id_especie = e.id_especie
        JOIN refugios r ON m.id_refugio = r.id_refugio
        LEFT JOIN razas ra ON m.id_raza = ra.id_raza
        WHERE m.id_mascota = :id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $mascota_id]);
    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mascota) {
        http_response_code(404);
        echo "<div class='p-5 text-center'>Mascota no encontrada.</div>";
        exit;
    }

    // ===========================================
    // 2. Consultar características (si es necesario)
    // ===========================================
    $caracteristicas_sql = "
        SELECT c.nombre_caracteristica 
        FROM mascota_caracteristicas mc
        JOIN caracteristicas c ON mc.id_caracteristica = c.id_caracteristica
        WHERE mc.id_mascota = :id
    ";
    $carac_stmt = $pdo->prepare($caracteristicas_sql);
    $carac_stmt->execute(['id' => $mascota_id]);
    $caracteristicas = $carac_stmt->fetchAll(PDO::FETCH_COLUMN);


    $url_foto = $mascota['url_foto'] ?? '';
    $img_path = "../assets/img/default_pet.jpg";
    if (!empty($url_foto) && strpos($url_foto, "/uploads/") !== false) {
        // La ruta es ../uploads/nombre.jpg si api_mascota.php está en /actions/
        $img_path = "../uploads/" . explode("/uploads/", $url_foto)[1];
    }
    
    // =================================
    // 3. Imprimir el HTML del MODAL
    // =================================
    ?>
<button class="closeModal" onclick="closeModal()">✕</button>

<div class="modal-body p-0">
    <div class="row g-0">
        <div class="col-md-5" style="min-height: 450px;">
            <img src="<?= $img_path ?>" class="img-fluid-cover" alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>">
        </div>

        <div class="col-md-7 p-4 d-flex flex-column justify-content-between">
            <div>
                <h2 class="h3 fw-bold text-primary mb-3"><?= htmlspecialchars($mascota['nombre']) ?></h2>

                <table class="table table-sm table-borderless small mb-4">
                    <tr>
                        <td class="fw-semibold text-secondary" style="width: 100px;">Especie:</td>
                        <td><?= htmlspecialchars($mascota['nombre_especie']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Raza:</td>
                        <td><?= htmlspecialchars($mascota['nombre_raza'] ?? 'Mestizo') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Sexo:</td>
                        <td><?= htmlspecialchars($mascota['sexo']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Edad:</td>
                        <td><?= htmlspecialchars($mascota['edad']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Tamaño:</td>
                        <td><?= htmlspecialchars($mascota['tamano']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Estado:</td>
                        <td><span class="badge bg-success"><?= htmlspecialchars($mascota['estado']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-secondary">Refugio:</td>
                        <td><?= htmlspecialchars($mascota['nombre_refugio']) ?></td>
                    </tr>
                </table>

                <h5 class="text-secondary fw-semibold mt-3 mb-2">Descripción</h5>
                <p class="text-muted small">
                    <?= nl2br(htmlspecialchars($mascota['descripcion'])) ?>
                </p>

                <?php if (!empty($caracteristicas)): ?>
                    <h5 class="text-secondary fw-semibold mt-3 mb-2">Características</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($caracteristicas as $carac): ?>
                            <span class="badge bg-success p-2 rounded-pill">
                                <i class="bi bi-check-circle-fill"></i>
                                <?= htmlspecialchars($carac) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <a href="../private/solicitar_adopcion.php?id=<?= $mascota_id ?>" class="btn btn-primary btn-lg mt-4 w-100">
                Solicitar Adopción
            </a>
        </div>

    </div>
</div>
<?php

} catch (Exception $e) { // Capturamos la excepción de conexión si ocurre
    http_response_code(500);
    error_log("Error Ficha Mascota: " . $e->getMessage());
    echo "<div class='p-5 text-center'>Error interno del servidor. Detalle: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>