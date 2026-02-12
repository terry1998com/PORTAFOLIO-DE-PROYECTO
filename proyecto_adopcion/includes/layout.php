<?php
// includes/layout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar funciones, DB y autenticación
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';

/**
 * Renderiza una página completa con header, contenido y footer
 * @param string $content_file Ruta al archivo de contenido (solo el cuerpo)
 * @param array $data Variables que se pasan a la vista
 */
function renderView(string $content_file, array $data = [])
{
    extract($data); // Convierte claves del array en variables
    include __DIR__ . '/header.php';
    include $content_file;
    include __DIR__ . '/footer.php';
}
