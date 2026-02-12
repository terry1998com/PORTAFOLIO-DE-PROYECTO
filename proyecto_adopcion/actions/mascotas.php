<?php
// Archivo: actions/mascotas.php

function obtenerMascotasDestacadas($pdo, $limite = 4) {
    try {
        // Consulta corregida para ordenar por ID (lo más nuevo primero)
        $sql = "SELECT 
                    m.*, 
                    e.nombre_especie, 
                    r.nombre_refugio,
                    ra.nombre_raza,
                    -- Subconsulta para obtener la foto principal
                    (SELECT url_foto FROM fotos_mascota fm WHERE fm.id_mascota = m.id_mascota AND fm.es_principal = 1 LIMIT 1) as url_foto
                FROM 
                    mascotas m
                JOIN 
                    especies e ON m.id_especie = e.id_especie
                JOIN 
                    refugios r ON m.id_refugio = r.id_refugio
                LEFT JOIN
                    razas ra ON m.id_raza = ra.id_raza
                WHERE 
                    m.estado_adopcion = 'Disponible'
                ORDER BY 
                    m.id_mascota DESC  -- ⬅️ CAMBIO CLAVE: Ordenar por ID asegura que lo último agregado salga primero
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>