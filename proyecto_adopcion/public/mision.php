<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestra Misión | Adopción de Perritos</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* --- Footer Stick to Bottom --- */
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #f4f4f9;
        }
        main {
            flex: 1;
        }

        /* Contenedor principal */
        .mision-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        h1 {
            color: #d9534f; 
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        h3 {
            color: #5cb85c;
            border-bottom: 2px solid #5cb85c;
            padding-bottom: 6px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        h4 {
            margin-top: 20px;
            color: #333;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        p, li {
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .icon-section {
            color: #ff7043;
        }

        /* Sección CTA */
        .cta-section {
            margin-top: 40px;
            padding: 25px;
            background-color: #fdf3cd;
            border-radius: 10px;
            text-align: center;
        }

        /* Botones CTA */
        .btn-adoptar {
            background-color: #28a745 !important;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff !important;
            transition: 0.3s;
        }
        .btn-adoptar:hover {
            background-color: #1f7f36 !important;
        }

        .btn-donar {
            background-color: #ff7043 !important;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff !important;
            transition: 0.3s;
        }
        .btn-donar:hover {
            background-color: #e1572f !important;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<main>
    <div class="mision-container">

        <h1><i class="bi bi-heart-pulse-fill icon-section"></i>Nuestra misión</h1>

        <p>En <strong>Adopta Amigo</strong>, nuestra misión es clara: ser el puente de esperanza entre los perritos que más lo necesitan y las familias listas para brindarles un hogar lleno de amor. No solo rescatamos; <strong>transformamos vidas</strong>.</p>

        <hr>

        <h3><i class="bi bi-paw-fill icon-section"></i>Los Tres Pilares de Nuestra Labor</h3>

        <h4><i class="bi bi-heart icon-section"></i>1. Rescate y Bienestar Integral</h4>
        <ul>
            <li><i class="bi bi-thermometer-half icon-section"></i><b>Salud y Seguridad:</b> Atención veterinaria completa: vacunas, desparasitación, esterilización y refugio seguro.</li>
            <li><i class="bi bi-hand-thumbs-up-fill icon-section"></i><b>Rehabilitación:</b> Apoyo emocional y conductual para superar traumas y volver a confiar.</li>
        </ul>

        <h4><i class="bi bi-house-heart icon-section"></i>2. Adopción Responsable</h4>
        <ul>
            <li><i class="bi bi-check-circle-fill icon-section"></i><b>Compatibilidad:</b> Evaluaciones para garantizar una adopción exitosa y estable.</li>
            <li><i class="bi bi-book icon-section"></i><b>Educación:</b> Asesoría a adoptantes sobre cuidados y responsabilidades.</li>
        </ul>

        <h4><i class="bi bi-people-fill icon-section"></i>3. Concientización Comunitaria</h4>
        <ul>
            <li><i class="bi bi-scissors icon-section"></i><b>Esterilización:</b> Promoción activa para reducir el abandono.</li>
            <li><i class="bi bi-mortarboard-fill icon-section"></i><b>Tenencia Responsable:</b> Educación y campañas para crear conciencia.</li>
        </ul>

        <hr>

        <h3><i class="bi bi-eye-fill icon-section"></i>Nuestra Visión</h3>
        <p>Soñamos con un mundo donde ningún perrito necesite vivir en las calles. Buscamos inspirar a la comunidad a actuar con compasión, y así romper el ciclo de abandono y maltrato.</p>

        <div class="cta-section">
            <h3>¡Sé parte del cambio!</h3>
            <a href="<?= $BASE_URL ?? '/proyecto_adopcion' ?>/public/catalogo.php" class="btn btn-adoptar me-3"><i class="bi bi-paw-fill"></i> Adoptar Hoy</a>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
