<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['rol'] === 'admin') {
    header("Location: panel_admin.php");
    exit;
} else {
    header("Location: panel_usuario.php");
    exit;
}
