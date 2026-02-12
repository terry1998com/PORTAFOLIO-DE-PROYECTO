<?php
// private/mascota_form.php - Estilizado con Bootstrap

require_once __DIR__ . '/../includes/header.php';
requireRefugioAdmin();

$refugio_id = $_SESSION['refugio_id'] ?? null;
if (!$refugio_id) {
    header('Location: /proyecto_adopcion/private/dashboard.php?error=no_refugio');
    exit;
}

$is_edit = isset($_GET['id']);
$mascota = [];
$caracteristicas_seleccionadas = [];
$foto_principal = '';
$form_title = $is_edit ? 'Editar Mascota' : 'Registrar Nueva Mascota';

$especies = $pdo->query("SELECT * FROM especies ORDER BY nombre_especie")->fetchAll(PDO::FETCH_ASSOC);
$razas = $pdo->query("SELECT * FROM razas ORDER BY nombre_raza")->fetchAll(PDO::FETCH_ASSOC);
$caracteristicas_disponibles = $pdo->query("SELECT * FROM caracteristicas ORDER BY nombre_caracteristica")->fetchAll(PDO::FETCH_ASSOC);

$tamanos = ['Pequeño', 'Mediano', 'Grande'];
$sexos = ['Macho', 'Hembra'];
$estados = ['Disponible', 'En Proceso', 'Adoptado'];

if ($is_edit) {

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        header('Location: /proyecto_adopcion/private/dashboard.php?error=id_invalido');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM mascotas WHERE id_mascota = ? AND id_refugio = ?");
    $stmt->execute([$id, $refugio_id]);
    $mascota = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mascota) {
        header('Location: /proyecto_adopcion/private/dashboard.php?error=no_encontrado');
        exit;
    }

    $stmtC = $pdo->prepare("SELECT id_caracteristica FROM mascotas_caracteristicas WHERE id_mascota = ?");
    $stmtC->execute([$id]);
    $caracteristicas_seleccionadas = $stmtC->fetchAll(PDO::FETCH_COLUMN);

    $stmtF = $pdo->prepare("SELECT url_foto FROM fotos_mascota 
                            WHERE id_mascota = ? AND es_principal = 1 LIMIT 1");
    $stmtF->execute([$id]);
    $foto_principal = $stmtF->fetchColumn();
}

// Los estilos CSS personalizados fueron eliminados y reemplazados por clases de Bootstrap.
?>

<div class="container py-5">
    <h2 class="fw-bold text-dark mb-1"><i class="bi bi-person-badge me-2"></i> <?= htmlspecialchars($form_title) ?></h2>
    <p class="text-muted fs-6 mb-4">Completa la siguiente información para continuar con el registro de la mascota.</p>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="/proyecto_adopcion/actions/mascota_save.php"
                method="POST"
                enctype="multipart/form-data"
                class="card p-4 shadow-lg border-0">

                <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? ''); ?>">

                <div class="mb-4 border-bottom pb-3">
                    <h3 class="fs-5 fw-semibold text-primary"><i class="bi bi-info-circle me-2"></i> Datos Generales</h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" required
                                class="form-control"
                                value="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Especie</label>
                            <select name="id_especie" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($especies as $e): ?>
                                    <option value="<?= $e['id_especie'] ?>"
                                        <?= (($mascota['id_especie'] ?? '') == $e['id_especie']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e['nombre_especie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Raza (opcional)</label>
                            <select name="id_raza" class="form-select">
                                <option value="">-- Seleccione Raza --</option>
                                <?php foreach ($razas as $r): ?>
                                    <option value="<?= $r['id_raza'] ?>"
                                        <?= (($mascota['id_raza'] ?? '') == $r['id_raza']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nombre_raza']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sexo</label>
                            <select name="sexo" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($sexos as $s): ?>
                                    <option value="<?= $s ?>" <?= (($mascota['sexo'] ?? '') == $s) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4 border-bottom pb-3">
                    <h3 class="fs-5 fw-semibold text-primary"><i class="bi bi-tag me-2"></i> Tamaño y Estado</h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tamaño</label>
                            <select name="tamano" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach ($tamanos as $t): ?>
                                    <option value="<?= $t ?>" <?= (($mascota['tamano'] ?? '') == $t) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado de Adopción</label>
                            <select name="estado_adopcion" class="form-select" required>
                                <?php foreach ($estados as $e): ?>
                                    <option value="<?= $e ?>" <?= (($mascota['estado_adopcion'] ?? 'Disponible') == $e) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4 border-bottom pb-3">
                    <h3 class="fs-5 fw-semibold text-primary"><i class="bi bi-clock me-2"></i> Edad</h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edad (años)</label>
                            <input type="number" class="form-control"
                                name="edad_anios"
                                min="0" max="30"
                                required
                                value="<?= htmlspecialchars($mascota['edad_anios'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edad (meses)</label>
                            <input type="number" class="form-control"
                                name="edad_meses"
                                min="0" max="11"
                                required
                                value="<?= htmlspecialchars($mascota['edad_meses'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-4 border-bottom pb-3">
                    <label class="form-label fw-bold fs-5 text-primary"><i class="bi bi-file-earmark-text me-2"></i> Descripción</label>
                    <textarea name="descripcion" rows="5"
                        class="form-control"
                        required><?= htmlspecialchars($mascota['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="mb-4 border-bottom pb-3">
                    <h3 class="fs-5 fw-semibold text-primary"><i class="bi bi-camera me-2"></i> Foto Principal</h3>

                    <?php if ($is_edit && $foto_principal): ?>
                        <p class="mb-2">Foto actual:</p>
                        <img src="<?= htmlspecialchars($foto_principal) ?>"
                            class="img-thumbnail mb-3"
                            style="max-width:200px; height:auto; object-fit:cover; border-radius:8px;">
                    <?php endif; ?>

                    <label for="foto_principal" class="form-label text-muted">Seleccionar nueva foto (PNG o JPG)</label>
                    <input type="file"
                        id="foto_principal"
                        name="foto_principal"
                        accept="image/jpeg,image/png"
                        class="form-control"
                        <?= $is_edit ? "" : "required" ?>>
                </div>

                <div class="mb-5">
                    <h3 class="fs-5 fw-semibold text-primary"><i class="bi bi-list-check me-2"></i> Características</h3>
                    <p class="text-muted small">Selecciona las características que aplican a esta mascota.</p>

                    <div class="row g-3">
                        <?php foreach ($caracteristicas_disponibles as $c): ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="caracteristicas[]"
                                        value="<?= $c['id_caracteristica'] ?>"
                                        id="carac<?= $c['id_caracteristica'] ?>"
                                        <?= in_array($c['id_caracteristica'], $caracteristicas_seleccionadas) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="carac<?= $c['id_caracteristica'] ?>">
                                        <?= htmlspecialchars($c['nombre_caracteristica']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold d-flex align-items-center justify-content-center">
                        <i class="bi bi-save me-2"></i> <?= $is_edit ? 'Guardar Cambios' : 'Registrar Mascota' ?>
                    </button>
                </div>

                <a href="/proyecto_adopcion/private/dashboard.php" class="text-center text-muted text-decoration-none">
                    Cancelar y Volver al Panel
                </a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>