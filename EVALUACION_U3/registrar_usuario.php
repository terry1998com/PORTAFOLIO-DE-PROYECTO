<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';

$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];

    if (empty($nombre) || empty($email) || empty($clave)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $existe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $error = "El correo ya existe.";
        } else {

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, email, clave, rol)
                VALUES (:nom, :email, :clave, 'usuario')
            ");

            $registrado = $stmt->execute([
                ':nom'   => $nombre,
                ':email' => $email,
                ':clave' => $claveHash
            ]);

            if ($registrado) {
                $mensaje = "Usuario registrado correctamente.";
            } else {
                $error = "Error al registrar. Intenta de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear cuenta</title>

    <style>
        body {
            background: #eef3fb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial;
        }

        .login-box {
            width: 430px;
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h1 {
            color: #0d47a1;
            margin-bottom: 20px;
            font-size: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            display: block;
            box-sizing: border-box;
            transition: 0.25s ease;
        }

        .btn-primary {
            background: #0d6efd;
            color: white;
            border: none;
            margin-top: 15px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
        }

        .btn-secondary {
            background: #f1f1f1;
            color: #333;
            margin-top: 12px;
            text-decoration: none;
            border: 1px solid #bdbdbd;
        }

        .btn-secondary:hover {
            background: #e4e4e4;
            border-color: #a9a9a9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .alert-success {
            padding: 10px;
            background: #c8f7c5;
            border-left: 4px solid #2ecc71;
            margin-bottom: 10px;
        }

        .alert-error {
            padding: 10px;
            background: #f8d7da;
            border-left: 4px solid #c0392b;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <h1>Crear Cuenta Nueva</h1>

        <?php if ($mensaje): ?>
            <div class="alert-success"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="clave" placeholder="Contraseña" required>

            <button type="submit" class="btn-primary">Registrar</button>
        </form>

        <a class="btn-secondary" href="login.php">Volver al Login</a>

    </div>

</body>

</html>
