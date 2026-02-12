<?php
// ==================== PUBLIC/PRENSA.PHP (SALA DE PRENSA) - DISEÑO ELEGANTE Y SOBRIO ====================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$BASE_URL = "/proyecto_adopcion";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prensa | AdoptaAmigo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* 🎨 PALETA DE COLORES ELEGANTE Y SOBRIA
         * Fondo: #f9f6f2
         * Primario (Café Oscuro): #4b2e18
         * Acento/Secundario (Naranja Terroso): #c78a4b
         */
        body {
            background-color: #f9f6f2; /* Fondo muy suave */
            color: #4b2e18; /* Texto principal oscuro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* HERO - Diseño limpio, color sólido */
        .hero {
            background-color: #4b2e18; /* Café oscuro como fondo */
            padding: 50px 20px; /* Reducción de padding para mayor sobriedad */
            text-align: center;
            color: #fff;
            border-radius: 12px;
            margin: 20px auto 40px auto;
            max-width: 1200px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .hero h1 {
            font-size: 2.5rem; /* Menos llamativo */
            font-weight: 700;
            margin-bottom: 5px;
            color: #e3a567; /* Acento claro para el título */
        }

        .hero p {
            font-size: 1.1rem;
            color: #f9f6f2;
        }

        /* NOTICIAS - Tarjetas blancas y limpias */
        .news-card {
            background: #ffffff; /* Fondo blanco para limpieza */
            border: 1px solid #eee; /* Borde muy sutil */
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
            transition: transform .3s, box-shadow .3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); /* Sombra más sutil */
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .news-card img {
            width: 100px; /* Tamaño ligeramente reducido */
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-top: 15px;
            border: 3px solid #c78a4b; /* Nuevo color de acento terroso */
            transition: transform .3s;
        }

        .news-card h5 {
            color: #4b2e18; /* Título en café oscuro */
            font-weight: 600;
            margin-top: 15px;
        }

        .news-card p {
            color: #555;
            font-size: 0.9rem;
        }

        .btn-read-more {
            background-color: #c78a4b; /* Naranja terroso sólido */
            border: none;
            color: white;
            font-weight: 600;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .btn-read-more:hover {
            background-color: #a86c32; /* Tono más oscuro al pasar el ratón */
            color: white;
        }

        /* MODALES */
        .modal-content {
            border-radius: 15px;
            padding: 20px;
        }

        .modal-content h4 {
            color: #4b2e18;
            font-weight: bold;
        }
        
        /* COMUNICADOS - Lista con acento sutil */
        .list-group-item {
            border-left: 5px solid #c78a4b; /* Línea de acento sutil */
            background: #ffffff; /* Fondo blanco */
            font-weight: 500;
            margin-bottom: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f3eee8;
        }

        .list-group-item i {
            color: #c78a4b;
            margin-right: 8px;
        }

        /* IMPACTO - Cajas con acento sólido */
        .impact-box {
            background-color: #c78a4b; /* Naranja terroso sólido */
            padding: 20px; /* Padding ligeramente reducido */
            border-radius: 12px;
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform .3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .impact-box i {
            font-size: 1.8rem; /* Tamaño de ícono ajustado */
            margin-bottom: 5px;
        }

        .impact-box:hover {
            transform: translateY(-5px);
        }

        /* CONTACTO PRENSA */
        .press-box {
            background: #fff; /* Fondo blanco limpio */
            padding: 30px;
            border-radius: 15px;
            border-left: 8px solid #c78a4b; /* Acento con nuevo color */
            text-align: center;
            margin-bottom: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-contact {
            background-color: #4b2e18; /* Botón de contacto en café oscuro para contraste */
            border: none;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-contact:hover {
            background-color: #6a4933;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="hero">
        <h1><i class="bi bi-newspaper"></i> Sala de Prensa</h1>
        <p>Noticias, campañas y comunicados oficiales de AdoptaAmigo.</p>
    </section>

    <div class="container mt-5">

        <h2 class="text-center fw-bold mb-4" style="color: #4b2e18;"><i class="bi bi-megaphone-fill"></i> Últimas Noticias</h2>
        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="news-card p-3">
                    <img src="https://placedog.net/600" alt="Noticia 1">
                    <h5><i class="bi bi-paw-fill"></i> Rescatamos 12 perritos</h5>
                    <p>Gracias a donaciones y voluntarios logramos darles refugio seguro.</p>
                    <button class="btn btn-read-more w-100" data-bs-toggle="modal" data-bs-target="#modal1">Leer más</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="news-card p-3">
                    <img src="https://placedog.net/650" alt="Noticia 2">
                    <h5><i class="bi bi-activity"></i> Campaña de esterilización</h5>
                    <p>Una iniciativa que ayudará a reducir la sobrepoblación canina.</p>
                    <button class="btn btn-read-more w-100" data-bs-toggle="modal" data-bs-target="#modal2">Leer más</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="news-card p-3">
                    <img src="https://placedog.net/620" alt="Noticia 3">
                    <h5><i class="bi bi-heart-fill"></i> Evento de adopciones</h5>
                    <p>30 familias encontraron a su nuevo compañero peludo.</p>
                    <button class="btn btn-read-more w-100" data-bs-toggle="modal" data-bs-target="#modal3">Leer más</button>
                </div>
            </div>

        </div>

        <div class="modal fade" id="modal1" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <h4>Rescate de 12 perritos</h4>
                    <p>El equipo de rescate acudió a un reporte vecinal donde se encontraron 12 perritos en situación de abandono. Fueron trasladados al refugio donde recibieron atención médica.</p>
                    <button class="btn btn-read-more w-100" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal2" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <h4>Campaña de esterilización</h4>
                    <p>La campaña comunitaria permitirá ofrecer servicios de esterilización a bajo costo para ayudar a controlar la población canina.</p>
                    <button class="btn btn-read-more w-100" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal3" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <h4>Evento de adopciones</h4>
                    <p>Gracias al trabajo de voluntarios y familias comprometidas, más de 30 lomitos encontraron un hogar amoroso.</p>
                    <button class="btn btn-read-more w-100" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>

        <hr class="my-5" style="border-color: #e3a567;">

        <h2 class="text-center fw-bold mb-4" style="color: #4b2e18;"><i class="bi bi-exclamation-circle-fill"></i> Comunicados Oficiales</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <ul class="list-group mt-3">
                    <li class="list-group-item"><i class="bi bi-clock-fill"></i> **Cambio de horario:** ahora abrimos de 9:00 AM a 6:00 PM.</li>
                    <li class="list-group-item"><i class="bi bi-info-circle-fill"></i> **Aviso:** se suspende temporalmente la adopción de cachorros.</li>
                    <li class="list-group-item"><i class="bi bi-building"></i> **Nueva alianza** con Veterinaria Patitas Felices.</li>
                </ul>
            </div>
        </div>
        
        <hr class="my-5" style="border-color: #e3a567;">

        <h2 class="text-center fw-bold mb-4" style="color: #4b2e18;"><i class="bi bi-bar-chart-fill"></i> Nuestro Impacto</h2>
        <div class="row text-center mt-4 g-4">
            <div class="col-md-3">
                <div class="impact-box"><i class="bi bi-paw-fill"></i> <span style="font-size: 1.5rem;">250+</span> Adopciones</div>
            </div>
            <div class="col-md-3">
                <div class="impact-box"><i class="bi bi-flag-fill"></i> <span style="font-size: 1.5rem;">80</span> Campañas</div>
            </div>
            <div class="col-md-3">
                <div class="impact-box"><i class="bi bi-people-fill"></i> <span style="font-size: 1.5rem;">30</span> Voluntarios</div>
            </div>
            <div class="col-md-3">
                <div class="impact-box"><i class="bi bi-basket-fill"></i> <span style="font-size: 1.5rem;">3T</span> de Alimento Donado</div>
            </div>
        </div>
        
        <hr class="my-5" style="border-color: #e3a567;">

        <h2 class="text-center fw-bold mb-4" style="color: #4b2e18;"><i class="bi bi-envelope-fill"></i> Contacto para Medios</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="press-box mt-3">
                    <p class="lead">Si eres periodista o representante de un medio, contáctanos para entrevistas o material oficial.</p>
                    <a href="mailto:contacto@adoptaamigo.com" class="btn btn-contact btn-lg fw-bold mt-3"><i class="bi bi-envelope-fill"></i> Enviar Correo</a>
                </div>
            </div>
        </div>

    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>