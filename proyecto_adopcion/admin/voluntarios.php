<?php
// ==================== ADMIN/VOLUNTARIOS.PHP ====================

// 1. Definir la página actual ANTES de incluir el header
$current_page = 'voluntarios'; 

// 2. Incluir el header.
require_once __DIR__ . '/../includes/header.php';

// ... (El resto de la lógica PHP se mantiene EXACTAMENTE igual)

// ⚡ Verificar que el usuario sea admin de refugio (rol 5) o global (rol 4)
// requireRefugioAdmin(); 

$refugio_id = $_SESSION['refugio_id'] ?? null;

try {
    if ($current_role == 5 && $refugio_id) {
        $stmt = $pdo->prepare("SELECT * FROM voluntarios ORDER BY fecha DESC");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM voluntarios ORDER BY fecha DESC");
        $stmt->execute();
    }
    
    $voluntarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar las solicitudes de voluntarios: " . $e->getMessage();
    $voluntarios = [];
}

/**
 * Renderiza el badge de estado usando clases y iconos de Bootstrap.
 * (Función mantenida sin cambios)
 */
function renderEstadoBadge($estado)
{
    $estado = $estado ?? 'Pendiente';
    $icon = '';
    $badgeClass = '';

    switch ($estado) {
        case 'Aprobado':
            $icon = 'bi-check-circle-fill';
            $badgeClass = 'bg-success';
            break;
        case 'Rechazado':
            $icon = 'bi-x-octagon-fill';
            $badgeClass = 'bg-danger';
            break;
        case 'Pendiente':
            $icon = 'bi-clock-fill';
            $badgeClass = 'bg-warning text-dark';
            break;
        default:
            $icon = 'bi-info-circle-fill';
            $badgeClass = 'bg-secondary';
    }

    return "<span class=\"badge rounded-pill {$badgeClass} p-2\"><i class=\"bi {$icon} me-1\"></i> " . ucfirst($estado) . "</span>";
}
?>

<main class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Gestión de Voluntarios</h2> 
            <p class="text-muted small">Revisa, aprueba o rechaza las solicitudes para tu refugio.</p>
        </div>
        <a href="<?= $BASE_URL ?>/admin/dashboard.php" class="btn btn-theme-back shadow-sm rounded-pill px-4 py-2">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

    <div class="mb-4">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['success']);
                                                                    unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['error']);
                                                                          unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 15%;"><i class="bi bi-person-badge-fill me-1"></i> Nombre</th>
                            <th style="width: 20%;"><i class="bi bi-envelope-fill me-1"></i> Email</th>
                            <th style="width: 30%;"><i class="bi bi-chat-square-text-fill me-1"></i> Mensaje</th>
                            <th style="width: 10%;"><i class="bi bi-calendar-check-fill me-1"></i> Fecha</th>
                            <th style="width: 10%;"><i class="bi bi-info-circle-fill me-1"></i> Estado</th>
                            <th style="width: 15%;"><i class="bi bi-gear-fill me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($voluntarios)): ?>
                            <?php foreach ($voluntarios as $v):
                                $estado_actual = $v['estado'] ?? 'Pendiente';
                            ?>
                                <tr>
                                    <td class="fw-bolder text-dark py-3"><?= htmlspecialchars($v['nombre']) ?></td>
                                    
                                    <td><a href="mailto:<?= htmlspecialchars($v['email']) ?>" class="text-decoration-none text-info small"><?= htmlspecialchars($v['email']) ?></a></td>

                                    <td data-bs-toggle="tooltip" data-bs-placement="top" title="<?= htmlspecialchars($v['mensaje']) ?>">
                                        <small class="text-secondary fst-italic"><?= htmlspecialchars(substr($v['mensaje'], 0, 40)) . (strlen($v['mensaje']) > 40 ? '...' : '') ?></small>
                                    </td>

                                    <td><span class="badge bg-light text-secondary fw-normal"><?= date('Y-m-d', strtotime($v['fecha'])) ?></span></td>

                                    <td>
                                        <?= renderEstadoBadge($estado_actual) ?>
                                    </td>

                                    <td>
                                        <?php if ($estado_actual === 'Pendiente'): ?>
                                            <div class="btn-group" role="group" aria-label="Acciones de Solicitud">
                                                <a href="<?= $BASE_URL ?>/admin/voluntarios_action.php?id=<?= $v['id'] ?>&action=aprobar"
                                                    class="btn btn-success btn-sm"
                                                    data-bs-toggle="tooltip" title="Aprobar Solicitud"
                                                    onclick="return confirm('¿Confirma aprobar la solicitud de voluntariado de <?= htmlspecialchars($v['nombre']) ?>?')">
                                                    <i class="bi bi-check-lg"></i> Aprobar
                                                </a>
                                                <a href="<?= $BASE_URL ?>/admin/voluntarios_action.php?id=<?= $v['id'] ?>&action=rechazar"
                                                    class="btn btn-danger btn-sm"
                                                    data-bs-toggle="tooltip" title="Rechazar Solicitud"
                                                    onclick="return confirm('¿Confirma rechazar la solicitud de voluntariado de <?= htmlspecialchars($v['nombre']) ?>?')">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-success small"><i class="bi bi-check2-all"></i> Proceso Finalizado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 bg-light">
                                    <p class="lead text-muted"><i class="bi bi-inbox-fill me-2"></i> **¡Bandeja de Voluntarios Vacía!** No hay solicitudes pendientes.</p>
                                    <small class="text-secondary">Vuelve a revisar más tarde.</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php
// 3. Incluir el footer. Contiene el cierre del <body> y <html> y los scripts JS
require_once __DIR__ . '/../includes/footer.php';
?>

<script>
    // Inicializar Tooltips de Bootstrap 
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>