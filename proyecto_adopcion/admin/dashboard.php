<?php
// admin/dashboard.php - Mejorado con Bootstrap 5 y Efecto Hover

require_once __DIR__ . '/../includes/header.php';
// Nota: requireRefugioAdmin() y el resto de la lógica PHP se mantienen intactos
requireRefugioAdmin();

$refugio_id = $_SESSION['refugio_id'] ?? null;
if (!$refugio_id) die("Error: El administrador no tiene un refugio asignado.");

$sql = "SELECT 
            m.id_mascota, m.nombre, m.tamano, m.estado_adopcion,
            e.nombre_especie, 
            r.nombre_raza,
            (SELECT url_foto FROM fotos_mascota 
             WHERE id_mascota = m.id_mascota AND es_principal = 1 
             LIMIT 1) AS url_foto
        FROM mascotas m
        JOIN especies e ON m.id_especie = e.id_especie
        LEFT JOIN razas r ON m.id_raza = r.id_raza
        WHERE m.id_refugio = ?
        ORDER BY m.fecha_ingreso DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$refugio_id]);
$mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if (!empty($_GET['msg']) && $_GET['msg'] === 'success') {
    // Uso de clase de alerta de Bootstrap
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Operación realizada con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
}
?>

<style>
    /* Efecto de transición general para los botones principales y de acción */
    .btn {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    /* Efecto Hover para los botones principales (Ver Solicitudes, Nueva Mascota) */
    .btn-primary:hover,
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Efecto Hover para los botones pequeños de la tabla (Editar, Eliminar) */
    /* Se cambió btn-info por btn-primary en la tabla, pero mantenemos el efecto hover para ambas clases */
    .btn-primary:hover,
    .btn-danger:hover {
        transform: scale(1.05);
        /* Ligeramente más grande */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 10;
        /* Asegura que el botón crecido no quede detrás de nada */
    }
</style>

<div class="container py-5">

    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">Panel de Administración del Refugio</h2>
        <p class="text-muted fs-6">Gestión completa de las mascotas registradas en tu refugio.</p>
    </div>

    <?= $message ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="fs-5 text-secondary mb-0">Mascotas Registradas (<span class="fw-bold text-dark"><?= count($mascotas) ?></span>)</h3>
        <div class="d-flex gap-2">
            <a href="/proyecto_adopcion/admin/solicitudes.php" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-list-check me-2"></i> Ver Solicitudes
            </a>
            <a href="/proyecto_adopcion/private/mascota_form.php" class="btn btn-success d-flex align-items-center">
                <i class="bi bi-plus-circle me-2"></i> Nueva Mascota
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 100px;">Foto</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Especie / Raza</th>
                            <th scope="col">Tamaño</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mascotas)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Aún no has registrado ninguna mascota.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mascotas as $m): ?>
                                <?php
                                $img = $m['url_foto'] ?: '/proyecto_adopcion/assets/img/default_pet.jpg';
                                // Definición de colores para el estado usando clases de Bootstrap (badge)
                                $estado_color = match ($m['estado_adopcion']) {
                                    'Adoptado' => 'danger',
                                    'Disponible' => 'success',
                                    default => 'warning',
                                };
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($img) ?>" class="rounded-3 shadow-sm" style="width:70px; height:70px; object-fit:cover;">
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($m['nombre']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($m['nombre_especie']) ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($m['nombre_raza'] ?: 'Mestizo') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($m['tamano']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $estado_color ?> py-2 px-3 fs-6">
                                            <?= htmlspecialchars($m['estado_adopcion']) ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2 justify-content-center">
                                            <a href="/proyecto_adopcion/private/mascota_form.php?id=<?= $m['id_mascota'] ?>"
                                                class="btn btn-primary text-white py-1 px-4 rounded-2"
                                                title="Editar Mascota">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/proyecto_adopcion/actions/mascota_delete.php?id=<?= $m['id_mascota'] ?>"
                                                onclick="return confirm('¿Seguro que deseas eliminar a <?= addslashes($m['nombre']) ?>? Esta acción es irreversible.');"
                                                class="btn btn-danger py-1 px-4 rounded-2"
                                                title="Eliminar Mascota">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>