<?php
// config/db.php - Código Corregido

$host = 'localhost';
$db   = 'proyecto_adopcion';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES     => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // SOLUCIÓN: Usar die() o exit() para detener el script sin un error fatal 500
    // Además, mostramos un mensaje que te ayuda a diagnosticar el problema de DB.
    die("<h1>Error de Conexión a la Base de Datos</h1>" . 
        "<p>Verifica que MySQL/MariaDB esté activo y que el nombre de la base de datos sea correcto.</p>" . 
        "Detalle: " . $e->getMessage());
}
// El archivo ahora termina sin lanzar excepciones si la conexión tiene éxito.
?>