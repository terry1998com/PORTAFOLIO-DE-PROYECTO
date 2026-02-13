<?php
session_start();
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Login Universidades</title>
    <link rel="stylesheet" href="./css/login_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
    <?php if ($msg): ?>
        <div class="banner-message" id="message">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="login-container">
        <div class="logo">
            <img src="./images/logo upqroo.png" alt="">
        </div>
        <h2 class="title">Iniciar Sesión</h2>
        <p class="subtitle">Inicie sesión con su usuario y contraseña</p>
        <form id="loginForm" action="./php/login.php" method="POST">
            <div class="input-group">
                <input type="text" required id="username" name="username">
                <label for="username">Usuario</label>
            </div>
            <div class="input-group">
                <input type="password" required id="password" name="password">
                <label for="password">Contraseña</label>
                <button type="button" id="togglePassword" class="eye-btn">
                    <span id="eyeIcon" class="material-symbols-outlined">visibility_off</span>
                </button>
            </div>
            <button type="submit" class="login-btn">Iniciar Sesión</button>
        </form>
    </div>
    <script src="./js/login_script.js"></script>
</body>
</html>
