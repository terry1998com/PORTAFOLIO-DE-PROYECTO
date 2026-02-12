<?php
// Archivo: admin/solicitudes.php - Mejorado con Bootstrap 5 y Bootstrap Icons

include '../helpers/auth.php';
requireRefugioAdmin(); // Seguridad: Solo admin de refugio

include '../includes/header.php';
require_once '../config/db.php';

$refugio_id = $_SESSION['refugio_id'];

// 1. Consultar Solicitudes
// Hacemos JOIN con mascotas (para filtrar por refugio) y usuarios (para ver quién solicita)
$sql = "SELECT 
            s.id_solicitud, 
            s.fecha_solicitud, 
            s.estado, 
            s.telefono_contacto, 
            s.motivo,
            u.nombre AS solicitante, 
            u.email,
            m.nombre AS nombre_mascota
        FROM 
            solicitudes_adopcion s
        JOIN 
            mascotas m ON s.id_mascota = m.id_mascota
        JOIN 
            usuarios u ON s.id_usuario = u.id_usuario
        WHERE 
            m.id_refugio = ?
        ORDER BY 
            s.fecha_solicitud DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$refugio_id]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Definición de colores y etiquetas para el estado usando Bootstrap
function getEstadoBadge($estado)
{
    return match ($estado) {
        'Pendiente' => '<span class="badge bg-warning text-dark px-3 py-2 fw-bold">Pendiente</span>',
        'Aprobada' => '<span class="badge bg-success px-3 py-2 fw-bold">Aprobada</span>',
        'Rechazada' => '<span class="badge bg-danger px-3 py-2 fw-bold">Rechazada</span>',
        default => '<span class="badge bg-secondary px-3 py-2 fw-bold">Desconocido</span>',
    };
}

?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="bi bi-file-text me-2"></i> Solicitudes de Adopción</h2>
        <a href="dashboard.php" class="btn btn-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>

    <hr class="mb-4">

    <div class="card shadow-sm border-0">
        <?php if (empty($solicitudes)): ?>
            <div class="card-body">
                <div class="alert alert-info mb-0" role="alert">
                    <i class="bi bi-info-circle me-2"></i> No tienes solicitudes de adopción pendientes para tus mascotas.
                </div>
            </div>
        <?php else: ?>

            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 text-muted">Total de Solicitudes: <span class="fw-bold text-dark"><?= count($solicitudes) ?></span></h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 100px;">Fecha</th>
                            <th scope="col">Mascota</th>
                            <th scope="col">Solicitante</th>
                            <th scope="col">Motivo (Resumen)</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $sol): ?>
                            <tr>
                                <td>
                                    <small class="text-muted"><?php echo date('d/m/Y', strtotime($sol['fecha_solicitud'])); ?></small>
                                </td>

                                <td class="fw-bold">
                                    <?php echo htmlspecialchars($sol['nombre_mascota']); ?>
                                </td>

                                <td>
                                    <i class="bi bi-person me-1"></i> <?php echo htmlspecialchars($sol['solicitante']); ?><br>
                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($sol['email']); ?></small><br>
                                    <small class="text-muted"><i class="bi bi-phone me-1"></i> <?php echo htmlspecialchars($sol['telefono_contacto']); ?></small>
                                </td>

                                <td>
                                    <span class="text-secondary small fst-italic">
                                        <?php echo htmlspecialchars(substr($sol['motivo'], 0, 60)) . (strlen($sol['motivo']) > 60 ? '...' : ''); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo getEstadoBadge($sol['estado']); ?>
                                </td>

                                <td class="text-center">
                                    <?php if ($sol['estado'] == 'Pendiente'): ?>
                                        <a href="../actions/solicitud_status.php?id=<?php echo $sol['id_solicitud']; ?>&status=Aprobada"
                                            class="btn btn-sm btn-success me-1" title="Aprobar Solicitud">
                                            <i class="bi bi-check2-circle"></i>
                                        </a>
                                        <a href="../actions/solicitud_status.php?id=<?php echo $sol['id_solicitud']; ?>&status=Rechazada"
                                            class="btn btn-sm btn-danger" title="Rechazar Solicitud"
                                            onclick="return confirm('¿Rechazar solicitud?');">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Procesada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>