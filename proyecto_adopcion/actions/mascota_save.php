<?php
// actions/mascota_save.php

require_once __DIR__ . '/../includes/header.php';
requireRefugioAdmin(); // Solo admins de refugio

$refugio_id = $_SESSION['refugio_id'] ?? null;
if (!$refugio_id) {
    die("Error: Refugio no encontrado.");
}

// ---------------------------------------------------
// 1. Recibir datos del formulario
// ---------------------------------------------------
$id_mascota = $_POST['id_mascota'] ?? null;
$nombre = trim($_POST['nombre'] ?? '');
$id_especie = $_POST['id_especie'] ?? null;

// Si la raza viene vacía, guardar NULL real
$id_raza = ($_POST['id_raza'] === "" ? null : $_POST['id_raza']);

$sexo = $_POST['sexo'] ?? '';
$tamano = $_POST['tamano'] ?? '';
$estado = $_POST['estado_adopcion'] ?? 'Disponible';
$descripcion = trim($_POST['descripcion'] ?? '');
$caracteristicas = $_POST['caracteristicas'] ?? [];

// ⭐ NUEVAS VARIABLES DE EDAD ⭐
$edad_anios = isset($_POST['edad_anios']) ? (int)$_POST['edad_anios'] : 0;
$edad_meses = isset($_POST['edad_meses']) ? (int)$_POST['edad_meses'] : 0;
// ⭐ FIN NUEVAS VARIABLES ⭐

// Validación básica
if (!$nombre || !$id_especie || !$sexo || !$tamano) {
    die("Error: Faltan campos obligatorios.");
}

// Limitar meses de 0 a 11
if ($edad_meses < 0 || $edad_meses > 11) {
    die("Error: Los meses deben estar entre 0 y 11.");
}

// ---------------------------------------------------
// 2. CREAR O EDITAR
// ---------------------------------------------------
$is_edit = ($id_mascota && is_numeric($id_mascota));

if ($is_edit) {

    // Confirmar que la mascota pertenece al refugio actual
    $stmt = $pdo->prepare("SELECT id_mascota FROM mascotas WHERE id_mascota = ? AND id_refugio = ?");
    $stmt->execute([$id_mascota, $refugio_id]);
    if (!$stmt->fetch()) {
        die("Error: No tienes permiso para editar esta mascota.");
    }

    // EDITAR
    $sql = "UPDATE mascotas
            SET nombre = ?, id_especie = ?, id_raza = ?, sexo = ?, tamano = ?, 
                estado_adopcion = ?, descripcion = ?, 
                edad_anios = ?, edad_meses = ?
            WHERE id_mascota = ? AND id_refugio = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nombre,
        $id_especie,
        $id_raza,
        $sexo,
        $tamano,
        $estado,
        $descripcion,
        $edad_anios,
        $edad_meses,
        $id_mascota,
        $refugio_id
    ]);
} else {

    // CREAR
    $sql = "INSERT INTO mascotas 
            (nombre, id_especie, id_raza, sexo, tamano, estado_adopcion, descripcion, id_refugio, fecha_ingreso, edad_anios, edad_meses)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nombre,
        $id_especie,
        $id_raza,
        $sexo,
        $tamano,
        $estado,
        $descripcion,
        $refugio_id,
        $edad_anios,
        $edad_meses
    ]);

    $id_mascota = $pdo->lastInsertId();
}

// ---------------------------------------------------
// 3. Manejar FOTO PRINCIPAL
// ---------------------------------------------------
if (!empty($_FILES['foto_principal']['name'])) {

    $file = $_FILES['foto_principal'];

    if (!is_uploaded_file($file['tmp_name'])) {
        die("Error: Archivo de imagen inválido.");
    }

    // Tamaño máximo 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        die("Error: La foto supera el tamaño máximo de 2MB.");
    }

    // Validar que sea realmente imagen y obtener MIME
    $image_info = @getimagesize($file['tmp_name']);
    if ($image_info === false) {
        die("Error: Archivo no es una imagen válida.");
    }

    $mime = $image_info['mime'];
    $allowed_mimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    if (!array_key_exists($mime, $allowed_mimes)) {
        die("Error: La foto debe ser JPG o PNG.");
    }

    $ext = $allowed_mimes[$mime];

    // Carpeta de almacenamiento
    $upload_dir = __DIR__ . '/../uploads/mascotas/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Nombre único
    $new_filename = "mascota_" . $id_mascota . "_" . time() . "." . $ext;
    $ruta_final = $upload_dir . $new_filename;

    if (!move_uploaded_file($file['tmp_name'], $ruta_final)) {
        die("Error: No se pudo mover la imagen subida.");
    }

    // Asegurar permisos seguros
    @chmod($ruta_final, 0644);

    // URL para guardar en DB
    $url_db = "/proyecto_adopcion/uploads/mascotas/" . $new_filename;

    // Eliminar foto principal previa
    $pdo->prepare("DELETE FROM fotos_mascota WHERE id_mascota = ? AND es_principal = 1")
        ->execute([$id_mascota]);

    // Insertar nueva foto principal
    $pdo->prepare("INSERT INTO fotos_mascota (id_mascota, url_foto, es_principal)\n                    VALUES (?, ?, 1)")
                    ->execute([$id_mascota, $url_db]);
}

// ---------------------------------------------------
// 4. Guardar características seleccionadas
// ---------------------------------------------------
$pdo->prepare("DELETE FROM mascotas_caracteristicas WHERE id_mascota = ?")
    ->execute([$id_mascota]);

foreach ($caracteristicas as $caract) {
    $pdo->prepare("INSERT INTO mascotas_caracteristicas (id_mascota, id_caracteristica)\n                    VALUES (?, ?)")
        ->execute([$id_mascota, $caract]);
}

// ---------------------------------------------------
// 5. Modal de éxito
// ---------------------------------------------------
$rol = $_SESSION['role_id'] ?? null;

$dash_admin = '/proyecto_adopcion/admin/dashboard.php?msg=success';
$dash_priv = '/proyecto_adopcion/private/dashboard.php?msg=success';

$success_url = ($rol == 5 || $rol == 4) ? $dash_admin : $dash_priv;
?>

<style>
    .modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-box {
        width: 360px;
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 5px 28px rgba(0, 0, 0, 0.25);
        animation: show 0.25s ease-out;
        position: relative;
        text-align: center;
    }

    @keyframes show {
        from {
            transform: scale(.85);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-close {
        position: absolute;
        right: 12px;
        top: 12px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e53935;
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success-icon {
        font-size: 60px;
        color: #43a047;
        margin-bottom: 10px;
    }
</style>

<div class="modal-bg" id="modalExito">
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal()">✕</button>

        <div class="success-icon">✔</div>

        <h2>Guardado con éxito</h2>
        <p style="margin-top:5px; font-size:15px;">La información de la mascota se guardó correctamente.</p>
    </div>
</div>

<script>
    function cerrarModal() {
        document.getElementById("modalExito").style.display = "none";
        window.location.href = "<?= $success_url ?>";
    }

    setTimeout(cerrarModal, 2200);

    document.addEventListener('keydown', e => {
        if (e.key === "Escape") cerrarModal();
    });

    document.querySelector('.modal-bg').addEventListener('click', e => {
        if (e.target.classList.contains('modal-bg')) cerrarModal();
    });
</script>

    @keyframes show {
        from {
            transform: scale(.85);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-close {
        position: absolute;
        right: 12px;
        top: 12px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e53935;
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success-icon {
        font-size: 60px;
        color: #43a047;
        margin-bottom: 10px;
    }
</style>

<div class="modal-bg" id="modalExito">
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal()">✕</button>

        <div class="success-icon">✔</div>

        <h2>Guardado con éxito</h2>
        <p style="margin-top:5px; font-size:15px;">La información de la mascota se guardó correctamente.</p>
    </div>
</div>

<script>
    function cerrarModal() {
        document.getElementById("modalExito").style.display = "none";
        window.location.href = "<?= $success_url ?>";
    }

    setTimeout(cerrarModal, 2200);

    document.addEventListener('keydown', e => {
        if (e.key === "Escape") cerrarModal();
    });

    document.querySelector('.modal-bg').addEventListener('click', e => {
        if (e.target.classList.contains('modal-bg')) cerrarModal();
    });
</script>