<?php
// public/mascota.php

include '../includes/header.php';

// 🟦 Verificar que exista conexión PDO
if (!isset($pdo)) {
    die("<p>Error: Conexión a la base de datos no definida.</p>");
}

// 🟦 Validar ID recibido
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<p>Mascota no especificada o ID inválido.</p>";
    include '../includes/footer.php';
    exit;
}

$id = (int) $_GET['id'];

// 🟦 1. Obtener datos generales de la mascota, especie, raza y refugio
$sql = "SELECT m.*, e.nombre_especie, r.nombre_refugio, 
               r.telefono AS tel_refugio, r.direccion, 
               ra.nombre_raza
        FROM mascotas m
        JOIN especies e ON m.id_especie = e.id_especie
        JOIN refugios r ON m.id_refugio = r.id_refugio
        LEFT JOIN razas ra ON m.id_raza = ra.id_raza
        WHERE m.id_mascota = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

// 🟦 Validar existencia
if (!$mascota) {
    echo "<h2>Mascota no encontrada</h2>";
    include '../includes/footer.php';
    exit;
}

// 🟦 2. Obtener fotos (ordenando por foto principal primero)
$stmtFotos = $pdo->prepare("
    SELECT url_foto 
    FROM fotos_mascota 
    WHERE id_mascota = ? 
    ORDER BY es_principal DESC
");
$stmtFotos->execute([$id]);
$fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

// 🟦 3. Obtener características para la personalidad
$stmtCar = $pdo->prepare("
    SELECT c.nombre_caracteristica 
    FROM mascotas_caracteristicas mc
    JOIN caracteristicas c ON mc.id_caracteristica = c.id_caracteristica
    WHERE mc.id_mascota = ?
");
$stmtCar->execute([$id]);
$caracteristicas = $stmtCar->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container mascota-page">

    <div class="mascota-detail-content">

        <!-- Galería -->
        <div class="mascota-gallery">
            <?php
            $mainImgUrl = (!empty($fotos)) 
                ? htmlspecialchars($fotos[0]['url_foto']) 
                : '/proyecto_adopcion/assets/img/default_pet.jpg';
            ?>

            <img src="<?= $mainImgUrl; ?>" 
                 id="main-photo" 
                 class="modal-main-img" 
                 alt="Foto de <?= htmlspecialchars($mascota['nombre']); ?>">

            <?php if (count($fotos) > 1): ?>
                <div class="modal-thumbnails">
                    <?php foreach ($fotos as $i => $foto): 
                        $thumb = htmlspecialchars($foto['url_foto']);
                        $active = ($i === 0) ? 'active' : '';
                    ?>
                        <img src="<?= $thumb; ?>" 
                             class="thumb-img <?= $active; ?>" 
                             onclick="changeMainPhoto(this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información -->
        <div class="mascota-info">
            <h1 class="modal-title"><?= htmlspecialchars($mascota['nombre']); ?></h1>

            <div class="modal-tags">
                <span class="tag-badge">🐾 <?= htmlspecialchars($mascota['nombre_especie']); ?></span>

                <?php
                $claseSexo = ($mascota['sexo'] === 'Macho') ? 'sexo-macho' : 'sexo-hembra';
                $iconSexo  = ($mascota['sexo'] === 'Macho') ? '♂' : '♀';
                ?>
                <span class="tag-badge <?= $claseSexo; ?>">
                    <?= $iconSexo . " " . htmlspecialchars($mascota['sexo']); ?>
                </span>

                <span class="tag-badge">📏 <?= htmlspecialchars($mascota['tamano']); ?></span>
                <span class="tag-badge">📅 Ingreso: <?= date('d/m/Y', strtotime($mascota['fecha_ingreso'])); ?></span>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Raza</span>
                    <span class="detail-value"><?= htmlspecialchars($mascota['nombre_raza'] ?? 'Mestizo'); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Edad Aprox.</span>
                    <span class="detail-value">A calcular...</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Refugio</span>
                    <span class="detail-value"><?= htmlspecialchars($mascota['nombre_refugio']); ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Ubicación</span>
                    <span class="detail-value"><?= htmlspecialchars($mascota['direccion']); ?></span>
                </div>
            </div>

            <!-- Personalidad -->
            <h4 style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">Personalidad</h4>

            <div class="personality-chart">
                <?php
                $baseCualidades = ['Amigable', 'Juguetón', 'Tranquilo'];

                foreach ($baseCualidades as $cualidad) {
                    $tiene = in_array($cualidad, $caracteristicas);
                    $porc = $tiene ? '90%' : '20%';

                    echo '
                        <div class="skill-bar">
                            <div class="skill-label">' . $cualidad . '</div>
                            <div class="skill-track">
                                <div class="skill-fill" style="width: ' . $porc . '"></div>
                            </div>
                        </div>
                    ';
                }
                ?>
            </div>

            <!-- Historia -->
            <div class="modal-history">
                <h4>Historia de <?= htmlspecialchars($mascota['nombre']); ?></h4>
                <p><?= nl2br(htmlspecialchars($mascota['descripcion'])); ?></p>
            </div>

            <!-- Contacto -->
            <div class="contact-box">
                <h4>¿Interesado en adoptar?</h4>
                <p>Contacta al refugio: <strong><?= htmlspecialchars($mascota['nombre_refugio']); ?></strong></p>
                <p>Teléfono: <?= htmlspecialchars($mascota['tel_refugio']); ?></p>

                <div class="modal-actions">
                    <?php if (!empty($mascota['tel_refugio'])): 
                        $tel = preg_replace('/[^0-9]/', '', $mascota['tel_refugio']);
                        $wa = "https://wa.me/{$tel}?text=Hola, estoy interesado en adoptar a " . urlencode($mascota['nombre']);
                    ?>
                        <a href="<?= $wa; ?>" class="btn-whatsapp" target="_blank" style="flex: 1;">
                            📱 WhatsApp
                        </a>
                    <?php endif; ?>

                    <a href="contacto.php?asunto=Adopcion+<?= urlencode($mascota['nombre']); ?>" 
                       class="btn-adopt-modal" style="flex: 2;">
                        🐾 ¡QUIERO ADOPTAR!
                    </a>
                </div>

                <?php if (!isLoggedIn()): ?>
                    <small style="display: block; text-align: center; margin-top: 10px; color: #999;">
                        Debes iniciar sesión para formalizar la solicitud.
                    </small>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
function changeMainPhoto(thumbnail) {
    const mainPhoto = document.getElementById('main-photo');
    mainPhoto.src = thumbnail.src;

    document.querySelectorAll('.thumb-img').forEach(img =>
        img.classList.remove('active')
    );
    thumbnail.classList.add('active');
}
</script>

<?php include '../includes/footer.php'; ?>
