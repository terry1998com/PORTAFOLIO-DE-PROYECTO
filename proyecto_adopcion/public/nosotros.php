<?php include '../includes/header.php'; ?>

<div class="container my-5" style="max-width: 900px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Título principal -->
    <h1 class="text-center mb-4 text-brown fw-bold">
        <i class="bi bi-people-fill"></i> Sobre Nosotros
    </h1>

    <!-- Descripción -->
    <p class="text-muted" style="font-size: 18px; line-height: 1.8; text-align: justify;">
        Adopta un Amigo es una plataforma creada para brindar una segunda oportunidad
        a las mascotas que no tienen un hogar. Nuestro objetivo es conectar refugios responsables con
        familias que desean adoptar y transformar la vida de un animal, generando un impacto positivo
        en la sociedad y en la vida de cada mascota.
    </p>

    <!-- Misión -->
    <h2 class="mt-5 text-brown"><i class="bi bi-bullseye"></i> Nuestra Misión</h2>
    <p class="text-muted" style="font-size: 17px; line-height: 1.7; text-align: justify;">
        Promover la adopción responsable, facilitar la comunicación entre refugios y adoptantes,
        y reducir el número de animales en situación de abandono mediante tecnología accesible,
        confiable y fácil de usar.
    </p>

    <!-- Qué hacemos -->
    <h2 class="mt-5 text-brown"><i class="bi bi-list-task"></i> ¿Qué Hacemos?</h2>
    <ul class="list-group list-group-flush mb-4">
        <li class="list-group-item list-hover"><i class="bi bi-paw-fill text-brown me-2"></i>Mostramos perfiles completos de mascotas listas para adopción.</li>
        <li class="list-group-item list-hover"><i class="bi bi-paw-fill text-brown me-2"></i>Facilitamos el contacto entre refugios y futuros adoptantes.</li>
        <li class="list-group-item list-hover"><i class="bi bi-paw-fill text-brown me-2"></i>Proveemos herramientas para que los refugios gestionen sus mascotas.</li>
        <li class="list-group-item list-hover"><i class="bi bi-paw-fill text-brown me-2"></i>Fomentamos la adopción responsable mediante información fiable.</li>
    </ul>

    <!-- Compromiso -->
    <h2 class="mt-4 text-brown"><i class="bi bi-heart-fill"></i> Nuestro Compromiso</h2>
    <p class="text-muted" style="font-size: 17px; line-height: 1.7; text-align: justify;">
        Cada mascota merece una vida digna, segura y llena de cariño. Por ello trabajamos para que más personas
        consideren la adopción como la mejor opción al momento de integrar un nuevo compañero a su hogar.
    </p>

    <!-- Bloque de estadísticas -->
    <div class="row text-center mt-4 g-3">
        <div class="col-md-4">
            <div class="card stat-hover p-3 rounded text-white" style="background: linear-gradient(135deg, #e58e26, #d17b1f);">
                <i class="bi bi-paw-fill fs-2 mb-2"></i>
                <h3>350+</h3>
                <p>Mascotas Adoptadas</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-hover p-3 rounded text-white" style="background: linear-gradient(135deg, #f6b93b, #e58e26);">
                <i class="bi bi-building fs-2 mb-2"></i>
                <h3>50+</h3>
                <p>Refugios Aliados</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-hover p-3 rounded text-white" style="background: linear-gradient(135deg, #d17b1f, #e58e26);">
                <i class="bi bi-clipboard-check fs-2 mb-2"></i>
                <h3>1200+</h3>
                <p>Mascotas Registradas</p>
            </div>
        </div>
    </div>

    <!-- Llamado a la acción -->
    <div class="text-center mt-5">
        <a href="catalogo.php" class="btn btn-warning btn-lg fw-bold px-5 btn-hover" style="background: linear-gradient(90deg, #e58e26, #d17b1f); border: none; box-shadow: 0 6px 18px rgba(0,0,0,0.2); transition: all 0.3s;">
            <i class="bi bi-eye-fill me-2"></i> Ver Mascotas Disponibles
        </a>
    </div>

</div>

<!-- Estilos adicionales -->
<style>
    .text-brown {
        color: #4b2e2b !important;
    }

    /* List items hover */
    .list-hover {
        border-left: 5px solid #e58e26;
        background: #fff7f0;
        transition: all 0.3s;
    }

    .list-hover:hover {
        background: #fff1e6;
        transform: translateX(5px);
    }

    /* Estadísticas hover */
    .stat-hover {
        transition: all 0.3s;
    }

    .stat-hover:hover {
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
    }

    /* Botón hover */
    .btn-hover:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    /* Texto y sombra en estadísticas */
    .stat-hover h3,
    .stat-hover p {
        margin: 0;
    }
</style>

<?php include '../includes/footer.php'; ?>