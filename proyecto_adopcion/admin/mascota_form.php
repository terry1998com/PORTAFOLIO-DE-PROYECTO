<?php
// admin/mascota_form.php

include '../helpers/auth.php';
requireRefugioAdmin();
include '../includes/header.php';

$is_edit = isset($_GET['id']);
$mascota = [];
$form_title = $is_edit ? 'Editar Mascota' : 'Registrar Mascota';
$refugio_id = $_SESSION['refugio_id'] ?? 0;

// Obtener opciones
$especies = $pdo->query("SELECT * FROM especies ORDER BY nombre_especie")->fetchAll();
$razas = $pdo->query("SELECT * FROM razas ORDER BY nombre_raza")->fetchAll();
$tamanos = ['Pequeño', 'Mediano', 'Grande'];
$sexos = ['Macho', 'Hembra'];
$estados = ['Disponible', 'En Proceso', 'Adoptado'];
$caracteristicas_disponibles = $pdo->query("SELECT * FROM caracteristicas ORDER BY nombre_caracteristica")->fetchAll();

$caracteristicas_seleccionadas = [];
$foto_principal = '';

if ($is_edit) {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM mascotas WHERE id_mascota = ? AND id_refugio = ?");
    $stmt->execute([$id, $refugio_id]);
    $mascota = $stmt->fetch();

    if (!$mascota) {
        header("Location: dashboard.php");
        exit;
    }

    $stmt_caract = $pdo->prepare("SELECT id_caracteristica FROM mascotas_caracteristicas WHERE id_mascota = ?");
    $stmt_caract->execute([$id]);
    $caracteristicas_seleccionadas = $stmt_caract->fetchAll(PDO::FETCH_COLUMN);

    $stmt_foto = $pdo->prepare("SELECT url_foto FROM fotos_mascota WHERE id_mascota = ? AND es_principal = 1");
    $stmt_foto->execute([$id]);
    $foto_principal = $stmt_foto->fetchColumn();
}

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<div class="container" style="padding: 40px;">

    <h2><?= $form_title ?></h2>
    <p>Completa la siguiente información para continuar</p>
    <hr>

    <form id="formMascota" action="../actions/mascota_save.php" method="POST" enctype="multipart/form-data"
        style="max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">

        <input type="hidden" name="id_mascota" value="<?= $mascota['id_mascota'] ?? '' ?>">

        <!-- DATOS GENERALES -->
        <h5><strong>Datos Generales</strong></h5>
        <div class="row mt-3">

            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control"
                    value="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Especie</label>
                <select name="id_especie" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($especies as $e): ?>
                        <option value="<?= $e['id_especie'] ?>"
                            <?= (($mascota['id_especie'] ?? '') == $e['id_especie']) ? 'selected' : '' ?>>
                            <?= $e['nombre_especie'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Raza (opcional)</label>
                <select name="id_raza" class="form-control">
                    <option value="">-- Seleccione Raza --</option>
                    <?php foreach ($razas as $r): ?>
                        <option value="<?= $r['id_raza'] ?>"
                            <?= (($mascota['id_raza'] ?? '') == $r['id_raza']) ? 'selected' : '' ?>>
                            <?= $r['nombre_raza'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($sexos as $s): ?>
                        <option value="<?= $s ?>"
                            <?= (($mascota['sexo'] ?? '') == $s) ? 'selected' : '' ?>>
                            <?= $s ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- TAMAÑO Y ESTADO -->
        <h5 class="mt-4"><strong>Tamaño y Estado</strong></h5>

        <div class="row mt-2">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tamaño</label>
                <select name="tamano" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php foreach ($tamanos as $t): ?>
                        <option value="<?= $t ?>"
                            <?= (($mascota['tamano'] ?? '') == $t) ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Estado de Adopción</label>
                <select name="estado_adopcion" class="form-control" required>
                    <?php foreach ($estados as $es): ?>
                        <option value="<?= $es ?>"
                            <?= (($mascota['estado_adopcion'] ?? 'Disponible') == $es) ? 'selected' : '' ?>>
                            <?= $es ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- EDAD -->
        <h5 class="mt-4"><strong>Edad</strong></h5>

        <div class="row mt-2">

            <div class="col-md-6 mb-3">
                <label class="form-label">Edad (años)</label>
                <input type="number" name="edad_anios" class="form-control"
                    min="0" max="30"
                    value="<?= htmlspecialchars($mascota['edad_anios'] ?? '') ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Edad (meses)</label>
                <input type="number" name="edad_meses" class="form-control"
                    min="0" max="11"
                    value="<?= htmlspecialchars($mascota['edad_meses'] ?? '') ?>" required>
            </div>
        </div>

        <!-- DESCRIPCIÓN -->
        <h5 class="mt-4"><strong>Descripción</strong></h5>
        <textarea name="descripcion" rows="4" class="form-control mb-3"
            required><?= htmlspecialchars($mascota['descripcion'] ?? '') ?></textarea>

        <!-- FOTO -->
        <h5><strong>Foto Principal</strong></h5>
        <?php if ($is_edit && $foto_principal): ?>
            <div>
                <img src="<?= $foto_principal ?>" style="max-width:150px; border-radius:5px; margin-bottom:10px;">
            </div>
        <?php endif; ?>

        <input type="file" name="foto_principal" class="form-control mb-4" accept="image/*"
            <?= !$is_edit ? 'required' : '' ?>>

        <!-- CARACTERÍSTICAS -->
        <h5><strong>Características</strong></h5>

        <div class="row mt-3">
            <?php foreach ($caracteristicas_disponibles as $c): ?>
                <div class="col-md-3 mb-2">
                    <label>
                        <input type="checkbox" name="caracteristicas[]"
                            value="<?= $c['id_caracteristica'] ?>"
                            <?= in_array($c['id_caracteristica'], $caracteristicas_seleccionadas) ? 'checked' : '' ?>>
                        <?= $c['nombre_caracteristica'] ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- BOTÓN -->
        <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal"
            data-bs-target="#confirmModal">
            <?= $is_edit ? 'Guardar Cambios' : 'Registrar Mascota' ?>
        </button>

        <a href="dashboard.php" class="d-block text-center mt-2">Cancelar y Volver</a>
    </form>
</div>

<!-- MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirmar Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                ¿Deseas guardar los datos de esta mascota?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-success"
                    onclick="document.getElementById('formMascota').submit();">
                    Confirmar
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'; ?>
