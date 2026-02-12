<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'usuario') {
    header("Location: login.php");
    exit;
}

$categoriaFiltro = $_GET['categoria'] ?? "";

$sql = "SELECT m.*, p.nombre AS prov_nombre
        FROM medicamentos m
        LEFT JOIN proveedores p ON m.proveedor_id = p.id";

$params = [];
if ($categoriaFiltro !== "") {
    $sql .= " WHERE m.categoria LIKE :cat";
    $params[':cat'] = '%' . $categoriaFiltro . '%';
}
$sql .= " ORDER BY m.nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$totalCarrito = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Panel Usuario - Medicamentos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #1E9A5D;
            --primary-dark: #157346;
            --btn-radius: 8px;
        }

        .header-right {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: var(--btn-radius);
            font-size: 15px;
            font-weight: 600;
            text-decoration: none !important;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white !important;
            height: 40px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
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
            transform: translateY(-2px);
        }

        .header input[type="text"] {
            height: 40px;
            padding: 0 14px;
            border-radius: var(--btn-radius);
            border: 1px solid #cbd5e1;
            font-size: 15px;
            width: 180px;
        }

        .btn-cart {
            padding: 8px 14px;
            border-radius: var(--btn-radius);
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: .2s;
            height: 38px;
        }

        .btn-cart:hover {
            background: var(--primary-dark);
        }

        .add-input {
            width: 60px;
            height: 38px;
            border-radius: var(--btn-radius);
            border: 1px solid #cbd5e1;
            text-align: center;
        }

        .cart-badge {
            background: #dc3545;
            color: white;
            font-size: 12px;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
            margin-left: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        table th {
            background: #f1f1f1;
            color: #333;
        }

        table tr:hover {
            background: #f9f9f9;
        }

        .empty {
            text-align: center;
            color: #666;
        }

        .form-agregar {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-agregar .add-input {
            margin: 0;
        }

        .form-agregar button {
            height: 38px;
        }

        .form-buscar {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-buscar {
            height: 38px;
            padding: 0 11px;
            border-radius: var(--btn-radius);
            border: 1px solid #cbd5e1;
            font-size: 15px;
        }

        .btn-buscar {
            height: 38px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0 14px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">

            <div class="header" style="align-items:flex-start;">
                <div>
                    <h1>Medicamentos Disponibles</h1>
                    <div style="color:#666;font-size:14px;">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></div>
                </div>

                <div class="header-right">

                    <form method="GET" class="form-buscar">
                        <input type="text" name="categoria" placeholder="Buscar categoría..." value="<?= htmlspecialchars($categoriaFiltro) ?>" class="input-buscar">
                        <button class="btn btn-primary btn-buscar" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    </form>


                    <a href="cart.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Carrito
                        <?php if ($totalCarrito > 0): ?>
                            <span class="cart-badge"><?= $totalCarrito ?></span>
                        <?php endif; ?>
                    </a>

                    <a href="logout.php" class="btn-secondary"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Cantidad Disponible</th>
                        <th>Precio</th>
                        <th>Proveedor</th>
                        <th>Agregar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($medicamentos) === 0): ?>
                        <tr>
                            <td colspan="6" class="empty">No hay medicamentos.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($medicamentos as $med): ?>
                        <tr>
                            <td><?= htmlspecialchars($med['nombre']) ?></td>
                            <td><?= htmlspecialchars($med['categoria']) ?></td>
                            <td><?= (int)$med['cantidad'] ?></td>
                            <td>$<?= number_format($med['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($med['prov_nombre'] ?? '-') ?></td>
                            <td>
                                <?php if ($med['cantidad'] > 0): ?>
                                    <form action="add_to_cart.php" method="POST" class="form-agregar">
                                        <input type="hidden" name="id" value="<?= $med['id'] ?>">
                                        <input type="number" name="cantidad" min="1" max="<?= $med['cantidad'] ?>" value="1" class="add-input">
                                        <button class="btn-cart" type="submit"><i class="fas fa-plus-circle"></i> Agregar</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#d9534f;font-weight:bold;">Agotado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

</body>

</html>