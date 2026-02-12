<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cart as $it) {
    $total += $it['precio'] * $it['qty'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Contenedor del carrito */
        .cart-container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header h1 {
            font-size: 26px;
            margin: 0;
        }

        /* Lista de items */
        .cart-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 10px;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item strong {
            font-size: 18px;
            color: #1f3b73;
        }

        .cart-item div {
            display: flex;
            flex-direction: column;
        }

        .cart-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Botón quitar */
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: white;
            border: 1px solid #d9534f;
            color: #d9534f;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-outline:hover {
            background: #d9534f;
            color: white;
        }

        /* Total */
        .total-box {
            text-align: right;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #1E9A5D;
        }

        /* Carrito vacío */
        .empty-cart {
            padding: 25px;
            text-align: center;
            background: #fafafa;
            border-radius: 10px;
            color: #777;
        }

        /* Botones secundarios */
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            background: linear-gradient(145deg, #a9a9a9, #7d7d7d);
            color: #ffffff;
            font-weight: bold;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            /* quitar subrayado */
        }

        .btn-secondary:hover {
            background: linear-gradient(145deg, #7d7d7d, #5a5a5a);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        /* Botón principal */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 12px 25px;
            background-color: #1E9A5D;
            color: #fff;
            font-weight: bold;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #157346;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="cart-container">

            <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1>Mi Carrito</h1>
                <a href="panel_usuario.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Seguir Comprando</a>
            </div>

            <?php if (empty($cart)): ?>
                <div class="empty-cart">Tu carrito está vacío.</div>

            <?php else: ?>

                <ul class="cart-list">
                    <?php foreach ($cart as $idx => $it): ?>
                        <li class="cart-item">
                            <div>
                                <strong><?= htmlspecialchars($it['nombre']) ?></strong>
                                <div style="font-size:14px;color:#555;">
                                    Cantidad: <?= (int)$it['qty'] ?> × $<?= number_format($it['precio'], 2) ?>
                                </div>
                            </div>

                            <div class="cart-actions">
                                <form method="POST" action="remove_from_cart.php" style="display:inline;">
                                    <input type="hidden" name="index" value="<?= $idx ?>">
                                    <button class="btn-outline"><i class="fas fa-trash"></i> Quitar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="total-box">
                    Total: $<?= number_format($total, 2) ?>
                </div>

                <form method="POST" action="solicitar.php" style="margin-top:20px;">
                    <button class="btn-primary"><i class="fas fa-check"></i> Solicitar Medicamentos</button>
                </form>

            <?php endif; ?>

        </div>
    </div>

</body>

</html>