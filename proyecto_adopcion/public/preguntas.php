<?php
// ==================== PUBLIC/PREGUNTAS.PHP (FAQ) - MÁS CHICO Y ELEGANTE ====================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$BASE_URL = "/proyecto_adopcion";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes - AdoptaAmigo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> 

    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">

    <style>
        /* Paleta de Colores de Referencia:
         * Primario (café oscuro): #4b2e18
         * Secundario (naranja cálido): #e3a567
         */
        body {
            background-color: #f9f6f2;
        }

        /* Banner de la sección: AJUSTE DE TAMAÑO (MUY COMPACTADO) */
        .faq-hero {
            background-color: #4b2e18;
            color: #fff;
            padding: 15px 0;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .faq-hero h1 {
            color: #e3a567;
            font-weight: 700;
            font-size: 1.75rem; 
            margin-bottom: 0.25rem !important;
        }
        
        .accordion-item {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .accordion-button {
            background-color: #f3c99f;
            color: #4b2e18;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 18px 20px;
            transition: background 0.3s;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e3a567;
            color: #fff;
            border-bottom: 1px solid #c9884e;
        }

        .accordion-body {
            background-color: #fff8f2;
            color: #5a3d2c;
            font-size: 1rem;
            line-height: 1.6;
        }

        .icon-question {
            /* Mantenemos el estilo para los nuevos iconos de Bootstrap */
            font-size: 1.4rem;
            margin-right: 15px;
            color: #4b2e18; 
            transition: color 0.3s;
        }

        .accordion-button:not(.collapsed) .icon-question {
            color: #fff;
        }

        .container-custom {
            max-width: 900px;
            margin-top: 20px;
            margin-bottom: 60px;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="faq-hero">
        <div class="container container-custom text-center">
            <h1 class="mb-1">Preguntas Frecuentes</h1>
            <p class="lead text-muted" style="font-size: 0.9rem; margin: 0;">Resolvemos las dudas más comunes sobre el proceso de adopción.</p> 
        </div>
    </div>

    <main class="container container-custom">
        <div class="accordion" id="faqAccordion">

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span class="icon-question"><i class="bi bi-list-ol"></i></span> ¿Cuál es el proceso para adoptar una mascota?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Primero debes **registrarte** en nuestro sitio, revisar las mascotas disponibles y llenar el **formulario de adopción**. Nuestro equipo evaluará tu solicitud y te contactará para completar el proceso.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <span class="icon-question"><i class="bi bi-people"></i></span> ¿Puedo adoptar más de una mascota a la vez?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sí, siempre y cuando puedas demostrar que tienes los **recursos y el espacio adecuado** para cuidar de todas ellas. Cada solicitud será evaluada individualmente para asegurar el bienestar de los animales.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <span class="icon-question"><i class="bi bi-file-earmark-check"></i></span> ¿Qué requisitos debo cumplir para adoptar?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Debes ser **mayor de edad**, contar con un **hogar seguro** y comprometerte a cuidar de la mascota a largo plazo. También se requiere identificación y, en algunos casos, una visita de pre-adopción y referencias.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <span class="icon-question"><i class="bi bi-house-heart"></i></span> ¿Puedo devolver la mascota si no me adapto?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sí, en AdoptaAmigo nos preocupamos por el bienestar de la mascota. Si surge algún problema grave de adaptación, contáctanos **de inmediato** (en lugar de reubicarla por tu cuenta) y te orientaremos sobre el protocolo de devolución segura a un refugio.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <span class="icon-question"><i class="bi bi-currency-dollar"></i></span> ¿Hay algún costo por la adopción?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Cobramos una **cuota de recuperación mínima** para cubrir gastos de esterilización, vacunas y desparasitación. Esto asegura que la mascota ha recibido atención veterinaria completa antes de ser entregada.
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>