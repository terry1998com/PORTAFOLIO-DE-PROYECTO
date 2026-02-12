<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: panel.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: panel.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT nombre FROM medicamentos WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    die("El medicamento no existe.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['confirmar'])) {

        $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: panel.php?msg=deleted");
        exit;
    } else {
        header("Location: panel.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Medicamento</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: #f2f6fb;
            font-family: "Segoe UI", sans-serif;
            margin: 0;
        }

        /* CENTRAR REAL del contenido */
        .content {
            margin-left: 240px;
            /* coincide con el ancho de tu sidebar */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 60px;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0 !important;
                padding: 20px;
            }
        }

        /* Caja centrada */
        .delete-box {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        h1 {
            font-size: 28px;
            color: #d32f2f;
            margin-bottom: 15px;
        }

        .warning-box {
            background: #fff5f5;
            border-left: 5px solid #d32f2f;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 16px;
            color: #b71c1c;
        }

        /* BOTONES IGUAL TAMAÑO */
        .btns {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btns>* {
            flex: 1;
            /* MISMO TAMAÑO REAL */
        }

        .btn-danger,
        .btn-secondary {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-align: center;
            display: block;
            transition: 0.3s;
        }

        .btn-danger {
            background: #d32f2f;
            color: white;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .btn-secondary {
            background: #e5eaf1;
            color: #1f3b73;
            text-decoration: none;
            line-height: normal;
        }

        .btn-secondary:hover {
            background: #d6dce6;
        }

        @media (max-width: 600px) {
            .btns {
                flex-direction: column;
            }
        }
    </style>

</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">

        <div class="delete-box">

            <h1>Eliminar Medicamento</h1>

            <div class="warning-box">
                Estás a punto de eliminar el medicamento:
                <br><b><?= htmlspecialchars($medicamento['nombre']) ?></b>
                <br><br>Esta acción no se puede deshacer.
            </div>

            <form method="POST">
                <div class="btns">
                    <button type="submit" name="confirmar" class="btn-danger">Eliminar</button>
                    <a href="panel.php" class="btn-secondary">Cancelar</a>
                </div>
            </form>

        </div>

    </div>

</body>

</html>