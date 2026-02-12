<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary: #1E9A5D;
            --primary-dark: #157346;
            --secondary: #e5e7eb;
            --secondary-text: #374151;
            --btn-radius: 12px;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .btn {
            padding: 10px 18px;
            border-radius: var(--btn-radius);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff !important;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--secondary-text) !important;
        }

        .btn-secondary:hover {
            background: #d1d5db;
            transform: translateY(-2px);
        }

        .btn-logout {
            background: #d9534f;
            color: #fff !important;
        }

        .btn-logout:hover {
            background: #c9302c;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: var(--btn-radius);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-edit {
            background: #007bff;
            color: #fff;
        }

        .btn-edit:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background: #bd2130;
            transform: translateY(-2px);
        }

        .table-actions {
            display: flex;
            gap: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 24px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-bar input[type="text"] {
            padding: 10px 14px;
            border-radius: var(--btn-radius);
            border: 1px solid #cbd5e1;
            font-size: 15px;
            width: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        table th {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">

            <div class="header">
                <h1>Panel Administrador — Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?></h1>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>

            <div class="search-bar">
                <form method="GET" style="display:flex; gap:10px;">
                    <input type="text" name="categoria" placeholder="Buscar por categoría..." value="<?= htmlspecialchars($categoriaFiltro) ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                </form>
                <a href="registro.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Medicamento</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicamentos as $med): ?>
                        <tr>
                            <td><?= htmlspecialchars($med['nombre']) ?></td>
                            <td><?= htmlspecialchars($med['categoria']) ?></td>
                            <td><?= $med['cantidad'] ?></td>
                            <td>$<?= number_format($med['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($med['prov_nombre']) ?></td>
                            <td class="table-actions">
                                <a class="btn btn-sm btn-edit" href="editar.php?id=<?= $med['id'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                <a class="btn btn-sm btn-delete" href="eliminar.php?id=<?= $med['id'] ?>" onclick="return confirm('¿Eliminar medicamento?')"><i class="fas fa-trash-alt"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

</body>

</html>