<?php
// Archivo: public/index.php

// -----------------------
// 1. Conexión a la base de datos
// -----------------------
require_once __DIR__ . '/../config/db.php'; // define $pdo

// -----------------------
// 2. Header y funciones
// -----------------------
include '../includes/header.php';
include '../actions/mascotas.php';

// -----------------------
// 3. Obtener últimas 4 mascotas destacadas
// -----------------------
$mascotas_destacadas = obtenerMascotasDestacadas($pdo, 4) ?? [];
?>

<style>
    /* ================= HERO ANIMADO ================= */
    .hero {
    position: relative;
    height: 500px;
    background-image: url('img/imagen.png');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Arial', sans-serif;
    overflow: hidden;
    border-radius: 20px;
    margin-bottom: 40px;
}

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        border-radius: 20px;
    }

    .hero-card {
        position: relative;
        z-index: 2;
        max-width: 700px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        padding: 40px 30px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 1s forwards;
    }

    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 38px rgba(0, 0, 0, 0.2);
    }

    .hero-card h1 {
        font-size: 2.8rem;
        margin-bottom: 15px;
        font-weight: 700;
        color: #333;
        line-height: 1.2;
    }

    .hero-card p {
        font-size: 1.25rem;
        margin-bottom: 25px;
        color: #555;
        line-height: 1.5;
    }

    .btn-hero {
        display: inline-block;
        background: linear-gradient(90deg, #ff7b26, #ffb03b);
        color: white;
        padding: 14px 28px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        transition: all 0.3s ease;
    }

    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.28);
        background: linear-gradient(90deg, #ff944d, #ffc066);
    }

    /* ================= TARJETAS ================= */
    .section-title {
        text-align: center;
        margin-top: 40px;
    }

    .grid-featured {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 26px;
        margin-top: 25px;
    }

    .feature-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        transition: .25s;
        border: 1px solid #e6e6e6;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.18);
    }

    .feature-img {
        width: 100%;
        height: 230px;
        object-fit: cover;
    }

    .feature-body {
        padding: 17px;
    }

    .feature-body h3 {
        margin: 5px 0;
        font-size: 1.3rem;
        font-weight: bold;
    }

    .feature-meta {
        color: #777;
        font-size: 0.95rem;
    }

    .btn-view {
        display: block;
        margin-top: 12px;
        background: #ff9933;
        color: white;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-view:hover {
        background: #e68628;
    }

    /* ================= MODAL ================= */
    .modal-overlay {
        position: fixed;
        inset: 0;
        display: none;
        justify-content: center;
        align-items: center;
        background: rgba(15, 15, 15, 0.6);
        backdrop-filter: blur(6px);
        z-index: 9999;
        overflow-y: auto;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal-box {
        width: 95%;
        max-width: 850px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(10, 10, 10, 0.45);
        animation: modalIn 0.3s ease;
        position: relative;
        overflow: hidden;
        margin: 20px 0;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .closeModal {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.4);
        color: #fff;
        border: none;
        font-size: 24px;
        cursor: pointer;
        z-index: 10;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .closeModal:hover {
        background: rgba(0, 0, 0, 0.7);
    }

    .img-fluid-cover {
        width: 100%;
        height: 100%;
        min-height: 400px;
        object-fit: cover;
    }

    .text-primary {
        color: #ff7b26 !important;
    }

    .btn-primary {
        background: linear-gradient(90deg, #ff7b26, #ffb03b) !important;
        border: none !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .img-fluid-cover {
            min-height: 250px;
        }
    }
</style>

<section class="hero">
    <div class="hero-overlay">
        <div class="hero-card">
            <h1>Encuentra a tu nuevo mejor amigo</h1>
            <p>Cientos de mascotas esperan un hogar amoroso.</p>
            <a href="catalogo.php" class="btn-hero">Ver Mascotas Disponibles</a>
        </div>
    </div>
</section>

<?php if (!empty($mascotas_destacadas)): ?>
    <section class="section-title">
        <h2>Últimas Mascotas Añadidas</h2>
        <p>Conoce a los nuevos integrantes que buscan un hogar.</p>
        <div class="container grid-featured">
            <?php foreach ($mascotas_destacadas as $m): ?>
                <?php
                $img = "../assets/img/default_pet.jpg";
                if (!empty($m['url_foto']) && strpos($m['url_foto'], "/uploads/") !== false) {
                    $img = "../uploads/" . explode("/uploads/", $m['url_foto'])[1];
                }
                ?>
                <div class="feature-card">
                    <a href="#" onclick="openModal(<?= $m['id_mascota'] ?>); return false;">
                        <img src="<?= $img ?>" class="feature-img" alt="<?= htmlspecialchars($m['nombre']) ?>">
                    </a>
                    <div class="feature-body">
                        <h3><?= htmlspecialchars($m['nombre']) ?></h3>
                        <p class="feature-meta"><?= htmlspecialchars($m['nombre_refugio']) ?></p>
                        <p class="feature-meta"><?= $m['nombre_especie'] ?> • <?= $m['tamano'] ?></p>
                        <a href="#" class="btn-view" onclick="openModal(<?= $m['id_mascota'] ?>); return false;">Ver Ficha</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- ================= MODAL ================= -->
<div id="modalMascota" class="modal-overlay" aria-hidden="true">
    <div class="modal-box">
        <button class="closeModal" onclick="closeModal()">✕</button>
        <div class="row g-0">
            <div class="col-md-5">
                <img id="modalImg" src="" class="img-fluid-cover" alt="Foto de Mascota">
            </div>
            <div class="col-md-7 p-4 d-flex flex-column justify-content-between">
                <div>
                    <h2 id="modalTitulo" class="h3 fw-bold text-primary mb-3"></h2>
                    <table class="table table-sm table-borderless small mb-4">
                        <tr>
                            <td class="fw-semibold text-secondary" style="width:100px;">Especie:</td>
                            <td id="modalEspecie"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Raza:</td>
                            <td id="modalRaza"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Sexo:</td>
                            <td id="modalSexo"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Edad:</td>
                            <td id="modalEdad"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Tamaño:</td>
                            <td id="modalTamano"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Estado:</td>
                            <td id="modalEstado"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-secondary">Refugio:</td>
                            <td id="modalRefugio"></td>
                        </tr>
                    </table>
                    <h5 class="text-secondary fw-semibold mt-3 mb-2">Descripción</h5>
                    <p id="modalDescripcion" class="text-muted small"></p>
                </div>
                <a href="#" id="modalAdoptarBtn" class="btn btn-primary btn-lg mt-4 w-100">Solicitar Adopción</a>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.body.style.overflow = 'hidden';
        fetch("get_mascota.php?id=" + id)
            .then(r => {
                if (!r.ok) throw new Error('Error de red');
                return r.json();
            })
            .then(data => {
                if (!data || data.error || !data.nombre) throw new Error(data.error || "No se recibieron datos válidos.");
                document.getElementById("modalTitulo").textContent = data.nombre;
                document.getElementById("modalDescripcion").innerHTML = data.descripcion ? data.descripcion.replace(/\n/g, '<br>') : 'Sin descripción.';
                let img = "../assets/img/default_pet.jpg";
                if (data.url_foto && data.url_foto.includes("/uploads/")) img = "../uploads/" + data.url_foto.split("/uploads/")[1];
                document.getElementById("modalImg").src = img;
                document.getElementById("modalEspecie").textContent = data.nombre_especie;
                document.getElementById("modalRaza").textContent = data.nombre_raza ?? "Mestizo";
                document.getElementById("modalSexo").textContent = data.sexo;
                document.getElementById("modalEdad").textContent = data.edad ?? "No especificada";
                document.getElementById("modalTamano").textContent = data.tamano;
                document.getElementById("modalEstado").textContent = data.estado;
                document.getElementById("modalRefugio").textContent = data.nombre_refugio;
                document.getElementById("modalAdoptarBtn").href = "../private/solicitar_adopcion.php?id=" + id;
                document.getElementById("modalMascota").classList.add("open");
            })
            .catch(err => {
                console.error(err);
                alert("No se pudo cargar la información de la mascota.");
                document.body.style.overflow = '';
            });
    }

    function closeModal() {
        document.getElementById("modalMascota").classList.remove("open");
        document.body.style.overflow = '';
    }
</script>

<?php include '../includes/footer.php'; ?>