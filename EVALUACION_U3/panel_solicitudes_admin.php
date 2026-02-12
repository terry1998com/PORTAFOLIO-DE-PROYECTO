<?php
session_start();
require 'db.php';

// Solo admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Cambiar estado si se recibe petición
if (isset($_POST['solicitud_id']) && isset($_POST['nuevo_estado'])) {
    $estadosValidos = ['pendiente', 'procesado', 'rechazado'];
    $nuevo_estado = strtolower(trim($_POST['nuevo_estado']));

    if (!in_array($nuevo_estado, $estadosValidos)) {
        $nuevo_estado = 'pendiente';
    }

    $stmt = $pdo->prepare("UPDATE solicitudes SET estado = :e WHERE id = :id");
    $stmt->execute([
        ':e' => $nuevo_estado,
        ':id' => $_POST['solicitud_id']
    ]);

    header("Location: panel_solicitudes_admin.php");
    exit;
}

// Obtener todas las solicitudes con nombre del usuario y total
$sql = "
    SELECT 
        s.id,
        s.fecha,
        s.estado,
        u.nombre AS usuario,
        COALESCE(SUM(si.cantidad * si.precio), 0) AS total
    FROM solicitudes s
    LEFT JOIN solicitud_items si ON si.solicitud_id = s.id
    LEFT JOIN usuarios u ON u.id = s.usuario_id
    GROUP BY s.id
    ORDER BY s.fecha DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitudes — Administrador</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .solicitud-card {
            background: white;
            padding: 20px;
            margin-bottom: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .solicitud-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            align-items: center;
        }

        .estado {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
        }

        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .estado-procesado {
            background: #d4edda;
            color: #155724;
        }

        .estado-rechazado {
            background: #f8d7da;
            color: #721c24;
        }

        .item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .total {
            margin-top: 12px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #1f3b73;
        }

        .action-buttons {
            margin-top: 12px;
            display: flex;
            gap: 10px;
        }

        .btn-secondary {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(145deg, #a9a9a9, #7d7d7d);
            color: #ffffff !important;
            font-weight: bold;
            text-decoration: none !important;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(145deg, #7d7d7d, #5a5a5a);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .btn-small {
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-pendiente {
            background: linear-gradient(145deg, #fff3cd, #ffe69c);
            color: #856404;
        }

        .btn-procesado {
            background: linear-gradient(145deg, #d4edda, #a8e0b3);
            color: #155724;
        }

        .btn-rechazado {
            background: linear-gradient(145deg, #f8d7da, #f1a1a1);
            color: #721c24;
        }

        .btn-pendiente:hover {
            background: linear-gradient(145deg, #ffe69c, #ffd23f);
            transform: translateY(-2px);
        }

        .btn-procesado:hover {
            background: linear-gradient(145deg, #a8e0b3, #77c98a);
            transform: translateY(-2px);
        }

        .btn-rechazado:hover {
            background: linear-gradient(145deg, #f1a1a1, #e57373);
            transform: translateY(-2px);
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1f3b73;
        }

        .header a.btn-secondary {
            margin-top: 10px;
            display: inline-block;
        }

        .empty {
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">

            <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1>Solicitudes de Usuarios</h1>
                <a href="panel.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
            </div>

            <?php if (empty($solicitudes)): ?>
                <div class="empty">No hay solicitudes registradas aún.</div>
            <?php else: ?>

                <?php foreach ($solicitudes as $sol): ?>

                    <?php
                    $stmt2 = $pdo->prepare("
            SELECT si.*, m.nombre 
            FROM solicitud_items si
            LEFT JOIN medicamentos m ON m.id = si.medicamento_id
            WHERE solicitud_id = :id
        ");
                    $stmt2->execute([':id' => $sol['id']]);
                    $items = $stmt2->fetchAll();
                    ?>

                    <div class="solicitud-card">

                        <div class="solicitud-header">
                            <div>
                                <strong>Solicitud #<?= $sol['id'] ?></strong><br>
                                <small>Usuario: <?= htmlspecialchars($sol['usuario']) ?></small>
                            </div>

                            <span class="estado estado-<?= strtolower($sol['estado']) ?>">
                                <?= ucfirst($sol['estado']) ?>
                            </span>
                        </div>

                        <div style="color:#666; margin-bottom:10px;">
                            Fecha: <?= $sol['fecha'] ?>
                        </div>

                        <?php foreach ($items as $it): ?>
                            <div class="item">
                                <strong><?= htmlspecialchars($it['nombre']) ?></strong><br>
                                Cantidad: <?= $it['cantidad'] ?> —
                                $<?= number_format($it['precio'], 2) ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="total">
                            Total: $<?= number_format($sol['total'], 2) ?>
                        </div>

                        <form method="POST" class="action-buttons">
                            <input type="hidden" name="solicitud_id" value="<?= $sol['id'] ?>">

                            <button name="nuevo_estado" value="pendiente" class="btn-small btn-pendiente">
                                <i class="fas fa-hourglass-start"></i> Pendiente
                            </button>

                            <button name="nuevo_estado" value="procesado" class="btn-small btn-procesado">
                                <i class="fas fa-check-circle"></i> Procesado
                            </button>

                            <button name="nuevo_estado" value="rechazado" class="btn-small btn-rechazado">
                                <i class="fas fa-times-circle"></i> Rechazado
                            </button>
                        </form>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>

</body>

</html>