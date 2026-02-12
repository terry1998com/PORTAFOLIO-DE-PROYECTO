<?php
// Archivo: public/get_mascota.php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Validar ID
$id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_mascota) {
    echo json_encode(['error' => 'ID de mascota no válido.']);
    exit;
}

try {

    // --- CONSULTAR MASCOTA ---
    // m.* selecciona todas las columnas de la tabla 'mascotas', incluyendo 'descripcion'
    $sql = "
        SELECT 
            m.*, 
            e.nombre_especie, 
            r.nombre_raza, 
            refu.nombre_refugio
        FROM mascotas m
        JOIN especies e ON m.id_especie = e.id_especie
        LEFT JOIN razas r ON m.id_raza = r.id_raza
        JOIN refugios refu ON m.id_refugio = refu.id_refugio
        WHERE m.id_mascota = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_mascota]);
    $m = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$m) {
        echo json_encode(['error' => 'Mascota no encontrada.']);
        exit;
    }

    // --- CONSULTAR FOTO PRINCIPAL ---
    $sql_foto = "SELECT url_foto FROM fotos_mascota WHERE id_mascota = ? AND es_principal = 1 LIMIT 1";
    $stmt_foto = $pdo->prepare($sql_foto);
    $stmt_foto->execute([$id_mascota]);

    $foto = $stmt_foto->fetchColumn();

    // Se mantiene la lógica de ruta de imagen (relativa a public/)
    if ($foto && strpos($foto, "/uploads/") !== false) {
        $foto = "../uploads/" . explode("/uploads/", $foto)[1];
    } else {
        $foto = "../assets/img/default_pet.jpg";
    }

    // --- FORMATEAR EDAD ---
    $anios = intval($m['edad_anios'] ?? 0);
    $meses = intval($m['edad_meses'] ?? 0);

    $edad = "No especificada";

    if ($anios > 0 && $meses > 0) {
        $edad = "$anios año(s) $meses mes(es)";
    } elseif ($anios > 0) {
        $edad = "$anios año(s)";
    } elseif ($meses > 0) {
        $edad = "$meses mes(es)";
    }

    // --- ENVÍO AL MODAL ---
    $data = [
        'nombre'            => $m['nombre'],
        'url_foto'          => $foto,
        'nombre_especie'    => $m['nombre_especie'],
        'nombre_raza'       => $m['nombre_raza'] ?: "Mestizo",
        'sexo'              => $m['sexo'],
        'edad'              => $edad,
        'tamano'            => $m['tamano'],
        'estado'            => $m['estado_adopcion'],
        'nombre_refugio'    => $m['nombre_refugio'],
        
        // ✅ LÍNEA AGREGADA: Incluye la descripción del registro de la base de datos
        'descripcion'       => $m['descripcion'] 
    ];

    echo json_encode($data);
} catch (PDOException $e) {
    // En producción, solo se debe mostrar un mensaje genérico.
    echo json_encode(['error' => "Error de base de datos: " . $e->getMessage()]);
}