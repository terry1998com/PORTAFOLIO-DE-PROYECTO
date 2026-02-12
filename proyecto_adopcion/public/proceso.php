<?php
// public/proceso.php - Página que detalla el proceso de adopción

require_once __DIR__ . '/../includes/header.php';
?>
<style>
    .proceso-container {
        padding: 35px;
        max-width: 900px;
        margin: 0 auto;
    }

    .proceso-card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .step-title {
        font-size: 24px;
        color: #2563eb;
        /* Color azul primario */
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 10px;
    }

    .step-number {
        display: inline-block;
        background: #059669;
        /* Color verde para el número */
        color: white;
        width: 35px;
        height: 35px;
        line-height: 35px;
        text-align: center;
        border-radius: 50%;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e0e0e0;
        gap: 10px;
    }

    .step-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
        transition: 0.2s;
    }

    .link:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .step-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .step-number {
            margin-bottom: 10px;
        }
    }
</style>

<div class="proceso-container">

    <h1 style="font-size: 36px; text-align: center; color: #333; margin-bottom: 10px;">
        Proceso de Adopción
    </h1>
    <p style="text-align: center; color: #666; font-size: 18px; margin-bottom: 40px;">
        Sigue estos sencillos pasos para darle un hogar amoroso a una mascota.
    </p>

    <div class="proceso-card">
        <?php
        $steps = [
            [
                'title' => 'Encuentra a tu Compañero Ideal',
                'description' => 'Explora nuestro <a class="link" href="/proyecto_adopcion/public/catalogo.php">catálogo de mascotas</a>. Utiliza los filtros de especie, tamaño y raza para encontrar a la mascota que mejor se adapte a tu estilo de vida y tu hogar.'
            ],
            [
                'title' => 'Envía tu Solicitud',
                'description' => 'Una vez que encuentres a tu mascota ideal, haz clic en su perfil y luego en el botón <strong>"Adoptar"</strong> para llenar el formulario de solicitud. Asegúrate de proporcionar toda la información de contacto de manera precisa.'
            ],
            [
                'title' => 'Revisión por el Refugio',
                'description' => 'El refugio revisará tu solicitud para asegurarse de que cumples con los requisitos básicos para la adopción. Este proceso puede tomar unos días. Puedes verificar el estado de tu solicitud en tu <strong>Panel Personal</strong>.'
            ],
            [
                'title' => 'Entrevista y Encuentro',
                'description' => 'Si tu solicitud es pre-aprobada, el refugio se pondrá en contacto contigo para coordinar una <strong>entrevista</strong> (presencial o virtual) y un <strong>encuentro</strong> con la mascota. ¡Es una oportunidad para conocerse mejor!'
            ],
            [
                'title' => 'Adopción Final y Compromiso',
                'description' => 'Tras la aprobación final, solo queda firmar el <strong>contrato de adopción</strong>, que establece las responsabilidades y el compromiso de cuidado. ¡Lleva a tu nuevo compañero a casa y empieza una vida juntos!'
            ],
        ];

        foreach ($steps as $index => $step):
        ?>
            <div class="step-item">
                <span class="step-number"><?= $index + 1 ?></span>
                <div>
                    <h2 class="step-title"><?= htmlspecialchars($step['title']) ?></h2>
                    <p><?= $step['description'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a class="link" href="/proyecto_adopcion/public/catalogo.php" style="font-size: 18px; padding: 10px 20px; border: 1px solid #2563eb; border-radius: 8px; display: inline-block;">
            Comenzar a Explorar Mascotas →
        </a>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>