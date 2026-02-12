<?php
// private/mi_voluntariado.php
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../config/db.php';

$BASE_URL = "/proyecto_adopcion";

// Obligamos login
requireLogin();

// Usuario actual
$user_id   = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? "Usuario";
$user_email = $_SESSION['email'] ?? null;

$error = null;
$solicitud = null;

if (!$user_id) {
    $error = "No se pudo obtener el ID del usuario. Reingrese al sistema.";
} else {
    try {
        $sql = "SELECT * FROM voluntarios WHERE id_usuario = ? ORDER BY fecha DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($solicitud) {
            $solicitud['disponibilidad_array'] = json_decode($solicitud['disponibilidad'] ?? '[]', true);
        }
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    }
}

// Configuración de estilos según estado
$display = [
    'style' => 'background:#e2e3e5; color:#383d41; border: 1px solid #d6d8db;',
    'title' => 'Estado Desconocido',
    'msg'   => 'Contacta al soporte si este estado persiste.',
    'color' => '#383d41'
];

if ($solicitud) {
    switch ($solicitud['estado']) {
        case 'Pendiente':
            $display = [
                'style' => 'background:#fff3cd; color:#856404; border:1px solid #ffeeba;',
                'title' => '¡Solicitud Pendiente!',
                'msg' => 'El equipo de administración revisará tu postulación y te contactará pronto.',
                'color' => '#856404'
            ];
            break;
        case 'Aprobado':
            $display = [
                'style' => 'background:#d4edda; color:#155724; border:1px solid #c3e6cb;',
                'title' => '¡Felicitaciones! Tu Solicitud fue Aprobada',
                'msg' => 'Un administrador se pondrá en contacto contigo para coordinar los próximos pasos.',
                'color' => '#155724'
            ];
            break;
        case 'Rechazado':
            $display = [
                'style' => 'background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;',
                'title' => 'Tu Solicitud fue Rechazada',
                'msg' => 'Lamentablemente, por el momento no podemos avanzar. Puedes intentar postularte nuevamente en el futuro.',
                'color' => '#721c24'
            ];
            break;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container container-vol" style="padding-top:30px; padding-bottom:50px;">
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <?php if (!$solicitud): ?>
        <div class="alert alert-info text-center">
            Aún no has enviado una solicitud de voluntariado.
            <a href="<?= $BASE_URL ?>/public/voluntariado.php" class="alert-link">¡Postúlate ahora!</a>
        </div>
    <?php else: ?>
        <h2 class="text-center mb-4">Tu Solicitud de Voluntariado</h2>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card p-4 card-vol">
                    <div class="text-center mb-3">
                        <span class="badge-vol" style="<?= $display['style'] ?>">Estado: <?= sanitize($solicitud['estado']) ?></span>
                        <h4 class="mt-2" style="color:<?= $display['color'] ?>;"><?= sanitize($display['title']) ?></h4>
                        <p class="text-muted"><?= sanitize($display['msg']) ?></p>
                    </div>

                    <hr>

                    <h5>Detalles enviados</h5>
                    <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($solicitud['fecha'])) ?></p>
                    <p><strong>Nombre:</strong> <?= sanitize($solicitud['nombre']) ?></p>
                    <p><strong>Email:</strong> <?= sanitize($solicitud['email']) ?></p>

                    <h5 class="mt-3">Disponibilidad</h5>
                    <p>
                        <?= !empty($solicitud['disponibilidad_array'])
                            ? implode(', ', array_map('sanitize', $solicitud['disponibilidad_array']))
                            : 'No especificada.' ?>
                    </p>

                    <button class="btn btn-secondary mt-3" data-bs-toggle="modal" data-bs-target="#mensajeModal">
                        Ver Mensaje Completo
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="mensajeModal">
            <div class="modal-dialog">
                <div class="modal-content card-vol">
                    <div class="modal-header">
                        <h5 class="modal-title">Mensaje enviado</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><?= nl2br(sanitize($solicitud['mensaje'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>