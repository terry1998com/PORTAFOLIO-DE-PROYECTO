<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";

$proveedores = $pdo->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int)$_POST['cantidad'];
    $precio = (float)$_POST['precio'];
    $proveedor_id = (int)$_POST['proveedor_id'];

    if ($nombre === "" || $categoria === "" || $cantidad < 0 || $precio < 0) {
        $error = "Completa todos los campos correctamente.";
    } else {
        $sql = "INSERT INTO medicamentos (nombre, categoria, cantidad, precio, proveedor_id)
                VALUES (:n, :c, :cant, :p, :prov)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([
            ":n" => $nombre,
            ":c" => $categoria,
            ":cant" => $cantidad,
            ":p" => $precio,
            ":prov" => $proveedor_id
        ])) {
            $mensaje = "Medicamento registrado correctamente.";
        } else {
            $error = "Error al registrar medicamento.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Medicamento</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .btn-secondary,
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        form input,
        form select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form button {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <div class="container">

            <h1>Registrar Medicamento</h1>
            <a href="panel_admin.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>

            <?php if ($mensaje): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $mensaje ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <label>Nombre del Medicamento:</label>
                <input type="text" name="nombre" required>

                <label>Categoría:</label>
                <input type="text" name="categoria" required>

                <label>Cantidad Disponible:</label>
                <input type="number" name="cantidad" min="0" required>

                <label>Precio Unitario:</label>
                <input type="number" step="0.01" min="0" name="precio" required>

                <label>Proveedor:</label>
                <select name="proveedor_id" required>
                    <option value="">Selecciona un proveedor</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Registrar Medicamento
                </button>
            </form>

        </div>
    </div>

</body>

</html>