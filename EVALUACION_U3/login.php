<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];

    if (empty($email) || empty($clave)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        $stmt = $pdo->prepare("SELECT id, nombre, clave, rol FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            header("Location: panel.php");
            exit;
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>

    <style>
        body {
            background: #f0f4fa;
            font-family: "Segoe UI", sans-serif;
        }

        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-box h1 {
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: bold;
            color: #1f3b73;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #cfd4d9;
        }

        .alert-error {
            background: #fde2e4;
            color: #c53030;
            border: 1px solid #f5b5b8;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            display: block;
            box-sizing: border-box;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn-primary {
            background: #276ef1;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 12px;
        }

        .btn-primary:hover {
            background: #174db8;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(39,110,241,0.4);
        }

        .btn-secondary {
            background: #e5eaf1;
            color: #1f3b73;
            text-decoration: none;
            border: 1px solid #b5c0d2;
        }

        .btn-secondary:hover {
            background: #d4d9e3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(31,59,115,0.3);
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-box">

            <h1>Iniciar Sesión</h1>

            <?php if ($error): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <input type="email"
                    name="email"
                    placeholder="Correo electrónico"
                    required>

                <input type="password"
                    name="clave"
                    placeholder="Contraseña"
                    required>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>

            <a href="registrar_usuario.php" class="btn-secondary">
                Crear cuenta nueva
            </a>

        </div>

    </div>

</body>

</html>
