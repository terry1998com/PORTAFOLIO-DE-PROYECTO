<?php
// Archivo: public/ficha_mascota.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

// 1. Obtener y validar el ID de la mascota
$id_mascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_mascota) {
    echo '<div class="alert alert-danger">ID de mascota no válido.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// 2. Consulta principal de la mascota
$sql_mascota = "
    SELECT 
        m.*, 
        e.nombre_especie,
        COALESCE(ra.nombre_raza, 'Mestizo') AS nombre_raza,
        ref.nombre_refugio
    FROM mascotas m
    JOIN especies e ON m.id_especie = e.id_especie
    LEFT JOIN razas ra ON m.id_raza = ra.id_raza
    JOIN refugios ref ON m.id_refugio = ref.id_refugio
    WHERE m.id_mascota = ?
";

$stmt_mascota = $pdo->prepare($sql_mascota);
$stmt_mascota->execute([$id_mascota]);
$mascota = $stmt_mascota->fetch();

if (!$mascota) {
    echo '<div class="alert alert-warning">Mascota no encontrada.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// 3. Consulta foto principal
$sql_foto = "SELECT url_foto FROM fotos_mascota 
             WHERE id_mascota = ? 
             ORDER BY es_principal DESC LIMIT 1";

$stmt_foto = $pdo->prepare($sql_foto);
$stmt_foto->execute([$id_mascota]);

$foto_principal = $stmt_foto->fetchColumn() 
    ?: '/proyecto_adopcion/assets/img/default_pet.jpg';

// 4. Consulta características
$sql_caract = "
    SELECT c.nombre_caracteristica 
    FROM mascotas_caracteristicas mc
    JOIN caracteristicas c ON mc.id_caracteristica = c.id_caracteristica
    WHERE mc.id_mascota = ?
";

$stmt_caract = $pdo->prepare($sql_caract);
$stmt_caract->execute([$id_mascota]);
$lista_caracteristicas = $stmt_caract->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- ============================= -->
<!-- ESTILOS MODERNOS Y ELEGANTES  -->
<!-- ============================= -->
<style>
    body {
        background: #f3f4f7;
    }

    .ficha-container {
        padding: 40px 15px;
    }

    .ficha-title {
        border-bottom: 3px solid #d9d9d9;
        padding-bottom: 12px;
        margin-bottom: 30px;
        font-weight: 700;
        font-size: 32px;
        letter-spacing: -0.5px;
    }

    /* Tarjetas */
    .card-custom {
        background: #ffffff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
    }

    .card-custom:hover {
        transform: translateY(-3px);
    }

    /* Botón Adoptar */
    .btn-adoptar {
        background: linear-gradient(135deg, #28a745, #23963e);
        color: white;
        padding: 14px;
        border-radius: 8px;
        text-align: center;
        display: block;
        font-size: 18px;
        font-weight: bold;
        margin-top: 22px;
        transition: 0.3s ease;
        box-shadow: 0 4px 10px rgba(40,167,69,0.25);
    }

    .btn-adoptar:hover {
        background: linear-gradient(135deg, #1e7e34, #1b732f);
        text-decoration: none;
        transform: translateY(-3px);
    }

    /* Imagen */
    .pet-image {
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.20);
    }

    /* Titulos secundarios */
    .section-title {
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }

    /* UL elegante */
    .nice-list li {
        margin-bottom: 6px;
        line-height: 1.5;
    }
</style>

<div class="container ficha-container">

    <h1 class="ficha-title">
        <?php echo htmlspecialchars($mascota['nombre']); ?>
    </h1>

    <div style="display: flex; gap: 35px; flex-wrap: wrap;">

        <!-- Columna izquierda -->
        <div style="flex: 1; min-width: 330px; max-width: 420px;">

            <img src="<?php echo htmlspecialchars($foto_principal); ?>"
                 alt="Foto de <?php echo htmlspecialchars($mascota['nombre']); ?>"
                 class="pet-image">

            <div class="card-custom" style="margin-top: 20px;">

                <p><strong>Especie:</strong> <?php echo htmlspecialchars($mascota['nombre_especie']); ?></p>
                <p><strong>Raza:</strong> <?php echo htmlspecialchars($mascota['nombre_raza']); ?></p>
                <p><strong>Tamaño:</strong> <?php echo htmlspecialchars($mascota['tamano']); ?></p>
                <p><strong>Sexo:</strong> <?php echo htmlspecialchars($mascota['sexo']); ?></p>

                <p><strong>Estado:</strong> 
                    <span style="font-weight: bold; color: 
                        <?php echo ($mascota['estado_adopcion'] === 'Disponible') ? 'green' : 'orangered'; ?>">
                        <?php echo htmlspecialchars($mascota['estado_adopcion']); ?>
                    </span>
                </p>

                <p><strong>Refugio:</strong> <?php echo htmlspecialchars($mascota['nombre_refugio']); ?></p>
                <p><strong>Fecha Ingreso:</strong> 
                    <?php echo date('d/m/Y', strtotime($mascota['fecha_ingreso'])); ?>
                </p>

            </div>

            <?php if ($mascota['estado_adopcion'] === 'Disponible'): ?>
                <a href="solicitar_adopcion.php?id_mascota=<?php echo $id_mascota; ?>" 
                   class="btn-adoptar">
                    ¡Adoptar a <?php echo htmlspecialchars($mascota['nombre']); ?>!
                </a>
            <?php endif; ?>
        </div>

        <!-- Columna derecha -->
        <div style="flex: 2; min-width: 330px;">

            <div class="card-custom">
                <h2 class="section-title">Historia y Descripción</h2>
                <p style="white-space: pre-wrap; line-height: 1.7;">
                    <?php echo htmlspecialchars($mascota['descripcion']); ?>
                </p>
            </div>

            <?php if (!empty($lista_caracteristicas)): ?>
                <div class="card-custom" style="margin-top: 25px;">
                    <h2 class="section-title">Características Destacadas</h2>
                    <ul class="nice-list">
                        <?php foreach ($lista_caracteristicas as $caract): ?>
                            <li><?php echo htmlspecialchars($caract); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
