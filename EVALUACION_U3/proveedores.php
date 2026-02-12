<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuevo_proveedor'])) {
    $nombre = trim($_POST["nombre"]);

    if ($nombre === "") {
        $error = "Debes ingresar un nombre para el proveedor.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO proveedores (nombre) VALUES (:n)");
        if ($stmt->execute([":n" => $nombre])) {
            $mensaje = "Proveedor registrado correctamente.";
        } else {
            $error = "Error al registrar proveedor.";
        }
    }
}

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM medicamentos WHERE proveedor_id = :id");
    $check->execute([":id" => $delId]);
    $enUso = $check->fetchColumn();

    if ($enUso > 0) {
        $error = "No puedes eliminar este proveedor porque está asignado a medicamentos.";
    } else {
        $del = $pdo->prepare("DELETE FROM proveedores WHERE id = :id");
        if ($del->execute([":id" => $delId])) {
            $mensaje = "Proveedor eliminado correctamente.";
        } else {
            $error = "Error al eliminar proveedor.";
        }
    }
}

$proveedores = $pdo->query("SELECT * FROM proveedores ORDER BY nombre ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .btn-secondary,
        .btn-primary,
        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            /* espacio entre filas */
        }

        table th,
        table td {
            padding: 12px 10px;
            text-align: left;
        }

        table th {
            background: #f1f1f1;
            color: #333;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }

        table tr {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .empty {
            text-align: center;
            color: #666;
            padding: 12px;
        }

        .btn-secondary {
            background: linear-gradient(145deg, #a9a9a9, #7d7d7d);
            color: #fff;
        }

        .btn-secondary:hover {
            background: linear-gradient(145deg, #7d7d7d, #5a5a5a);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(145deg, #28a745, #1e7e34);
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #1e7e34, #145c24);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: linear-gradient(145deg, #dc3545, #a71d2a);
            color: #fff;
            font-size: 14px;
            padding: 6px 12px;
        }

        .btn-delete:hover {
            background: linear-gradient(145deg, #a71d2a, #7d101f);
            transform: translateY(-2px);
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        form input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form button {
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
        }

        table th {
            background: #f1f1f1;
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

            <h1>Gestión de Proveedores</h1>
            <a href="panel_admin.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>

            <?php if ($mensaje): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $mensaje ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
            <?php endif; ?>

            <h2>Agregar Proveedor</h2>

            <form method="POST">
                <input type="hidden" name="nuevo_proveedor" value="1">

                <label>Nombre del proveedor:</label>
                <input type="text" name="nombre" required>

                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-plus-circle"></i> Registrar Proveedor
                </button>
            </form>

            <hr style="margin: 35px 0;">

            <h2>Lista de Proveedores</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (count($proveedores) === 0): ?>
                        <tr>
                            <td colspan="2" class="empty">No hay proveedores registrados.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($proveedores as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td>
                                <a href="proveedores.php?delete=<?= $p['id'] ?>"
                                    class="btn btn-delete"
                                    onclick="return confirm('¿Eliminar este proveedor?');">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>
    </div>

</body>

</html>