<?php
// Archivo: actions/auth.php

session_start();

// Conexión a la base de datos
require_once __DIR__ . '/../config/db.php';


// -----------------------------------------------------------------------------
// 🟦 LOGIN
// -----------------------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'login') {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        header('Location: ../public/login.php?error=Campos vacíos');
        exit;
    }

    // 🔴 CONSULTA CORREGIDA: SOLO PEDIMOS LOS DATOS NECESARIOS DE LA TABLA 'usuarios'
    $sql = "SELECT id_usuario, nombre, password_hash, id_rol 
            FROM usuarios 
            WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validaciones de contraseña
    if (!$user || !password_verify($password, $user['password_hash'])) {
        header('Location: ../public/login.php?error=Credenciales incorrectas');
        exit;
    }

    // LOGIN EXITOSO → GUARDAR DATOS
    $_SESSION['user_id']   = $user['id_usuario'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['role_id']   = $user['id_rol']; // ¡Aquí se guarda el rol 6 para Amisita!


    // ------------------------------------------------------------
    // ADMIN DE REFUGIO (ROL 5)
    // ------------------------------------------------------------
    if ($user['id_rol'] == 5) {

        // BUSCAR id_refugio en la tabla 'refugios'
        $sql2 = "SELECT id_refugio FROM refugios WHERE id_usuario_admin = ?";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$user['id_usuario']]);
        $refugio_id = $stmt2->fetchColumn();

        if (!$refugio_id) {
            // NO TIENE REFUGIO ASIGNADO
            header("Location: ../public/login.php?error=no_refugio_asignado");
            exit;
        }

        // Guardar refugio asignado
        $_SESSION['refugio_id'] = $refugio_id;
        $_SESSION['user_role'] = 'refugio_admin';

        header("Location: ../admin/dashboard.php");
        exit;
    }

    // ------------------------------------------------------------
    // SUPER ADMIN (ROL 4)
    // ------------------------------------------------------------
    if ($user['id_rol'] == 4) {
        $_SESSION['user_role'] = 'super_admin';
        header("Location: ../private/dashboard.php");
        exit;
    }

    // ------------------------------------------------------------
    // ADOPTANTE / USUARIO NORMAL (ROL 6 y otros)
    // ------------------------------------------------------------
    $_SESSION['user_role'] = 'adoptante';
    header("Location: ../public/index.php?success=Bienvenido");
    exit;
}


// -----------------------------------------------------------------------------
// 🟩 REGISTRO
// -----------------------------------------------------------------------------
elseif (isset($_POST['action']) && $_POST['action'] === 'register') {

    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!$nombre || !$email || !$password) {
        header("Location: ../public/registro.php?error=Campos vacíos");
        exit;
    }

    // Hash de contraseña
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    // Rol por defecto: adoptante (6)
    $sql = "INSERT INTO usuarios (id_rol, email, password_hash, nombre)
            VALUES (6, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $pass_hash, $nombre]);

        header("Location: ../public/login.php?success=Cuenta creada correctamente");
        exit;

    } catch (PDOException $e) {
        header("Location: ../public/registro.php?error=El correo ya está registrado");
        exit;
    }
}


// -----------------------------------------------------------------------------
// 🟥 LOGOUT
// -----------------------------------------------------------------------------
elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {

    session_unset();
    session_destroy();

    header("Location: ../public/index.php");
    exit;
}
?>