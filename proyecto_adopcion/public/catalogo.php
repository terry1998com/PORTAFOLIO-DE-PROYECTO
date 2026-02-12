<?php
// public/catalogo.php

include '../includes/header.php';
require_once '../config/db.php';

// Filtrado de búsqueda
$filter_especie = $_GET['especie'] ?? '';
$filter_raza = $_GET['raza'] ?? '';

// CONSULTAR MASCOTAS DISPONIBLES
try {
    $sql = "SELECT 
                m.*, 
                e.nombre_especie, 
                r.nombre_refugio,
                ra.nombre_raza,
                (SELECT url_foto FROM fotos_mascota fm 
                    WHERE fm.id_mascota = m.id_mascota 
                    AND fm.es_principal = 1 
                    LIMIT 1) as url_foto
            FROM mascotas m
            JOIN especies e ON m.id_especie = e.id_especie
            JOIN refugios r ON m.id_refugio = r.id_refugio
            LEFT JOIN razas ra ON m.id_raza = ra.id_raza
            WHERE m.estado_adopcion = 'Disponible' ";

    $params = [];
    if ($filter_especie) {
        $sql .= " AND m.id_especie = :especie ";
        $params[':especie'] = $filter_especie;
    }
    if ($filter_raza) {
        $sql .= " AND m.id_raza = :raza ";
        $params[':raza'] = $filter_raza;
    }

    $sql .= " ORDER BY m.fecha_ingreso DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ----------------------------------------------------
    // LÓGICA DE FILTROS ACTUALIZADA (PHP)
    // ----------------------------------------------------
    $especies = $pdo->query("SELECT id_especie, nombre_especie FROM especies ORDER BY nombre_especie")->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de razas: solo trae las razas de la especie seleccionada si existe el filtro.
    $sql_razas = "SELECT id_raza, nombre_raza FROM razas ";
    $params_razas = [];

    if ($filter_especie) {
        // Filtramos las razas por la especie seleccionada
        $sql_razas .= " WHERE id_especie = :especie_raza ";
        $params_razas[':especie_raza'] = $filter_especie;
    }
    $sql_razas .= " ORDER BY nombre_raza";

    $stmt_razas = $pdo->prepare($sql_razas);
    $stmt_razas->execute($params_razas);
    $razas = $stmt_razas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mascotas = [];
    error_log("Error de base de datos en catalogo.php: " . $e->getMessage());
}
?>

<style>
    /* ---------------- ESTILOS EXISTENTES ---------------- */
    .catalog-title {
        text-align: center;
        padding: 45px 20px;
        background: #f3f4f6;
    }

    .catalog-title h1 {
        font-size: 32px;
        font-weight: 800;
    }

    .catalog-title p {
        font-size: 17px;
        color: #555;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .grid-catalog {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 32px;
    }

    .pet-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        transition: .25s;
        border: 1px solid #e6e6e6;
        display: flex;
        flex-direction: column;
    }

    .pet-card:hover {
        transform: translateY(-5px);
    }

    .pet-img {
        width: 100%;
        height: 230px;
        object-fit: cover;
    }

    .pet-info {
        padding: 17px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .pet-meta {
        color: #777;
        font-size: .95rem;
    }

    .btn-view {
        background: #ff9933;
        padding: 10px;
        color: white;
        border-radius: 6px;
        text-align: center;
        text-decoration: none;
        font-weight: bold;
    }

    .search-bar {
        max-width: 720px;
        margin: 30px auto;
        display: flex;
        gap: 12px;
        background: #f8fafc;
        padding: 14px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .search-bar select,
    .search-bar button {
        padding: 10px 14px;
        border-radius: 10px;
    }

    .search-bar button {
        background: #2563eb;
        color: white;
        border: none;
    }

    /* ---------------- MODAL (COPIADO de index.php) ---------------- */
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
        padding: 0;
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

<section class="catalog-title">
    <h1>Catálogo de Adopción</h1>
    <p>Encuentra a tu próximo compañero entre todos nuestros amigos peludos.</p>
</section>

<form class="search-bar" method="GET" id="searchFilterForm">
    <select name="especie" id="selectEspecie">
        <option value="">Todas las especies</option>
        <?php foreach ($especies as $e): ?>
            <option value="<?php echo $e['id_especie'] ?>" <?php echo ($filter_especie == $e['id_especie'] ? 'selected' : '') ?>>
                <?php echo $e['nombre_especie'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="raza">
        <option value="">Todas las razas</option>
        <?php foreach ($razas as $r): ?>
            <option value="<?php echo $r['id_raza'] ?>" <?php echo ($filter_raza == $r['id_raza'] ? 'selected' : '') ?>>
                <?php echo $r['nombre_raza'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Buscar</button>
</form>

<div class="container">
    <div class="grid-catalog">

        <?php if (empty($mascotas)): ?>
            <p class="text-center w-100 fs-5 text-muted">No se encontraron mascotas disponibles con esos filtros.</p>
        <?php endif; ?>

        <?php foreach ($mascotas as $m): ?>
            <?php
            $img = "../assets/img/default_pet.jpg";
            if (!empty($m['url_foto'])) {
                if (strpos($m['url_foto'], "/uploads/") !== false) {
                    $img = "../uploads/" . explode("/uploads/", $m['url_foto'])[1];
                } else {
                    $img = $m['url_foto'];
                }
            }
            ?>
            <div class="pet-card">
                <a href="#" onclick="openModal(<?php echo $m['id_mascota'] ?>);return false;">
                    <img src="<?php echo $img ?>" class="pet-img" alt="Mascota <?php echo htmlspecialchars($m['nombre']) ?>">
                </a>
                <div class="pet-info">
                    <h3><?php echo htmlspecialchars($m['nombre']) ?></h3>
                    <div class="pet-meta">
                        <?php echo htmlspecialchars($m['nombre_refugio']) ?><br>
                        <?php echo $m['nombre_especie'] ?> • <?php echo $m['tamano'] ?>
                    </div>
                    <a href="#" class="btn-view" onclick="openModal(<?php echo $m['id_mascota'] ?>);return false;">
                        Ver ficha
                    </a>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
</div>

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
                            <td class="fw-semibold text-secondary" style="width: 100px;">Especie:</td>
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

                <a href="#" id="modalAdoptarBtn" class="btn btn-primary btn-lg mt-4 w-100">
                    Solicitar Adopción
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.body.style.overflow = 'hidden';

        // ⚠️ RUTA CRÍTICA: Asume que get_mascota.php está en la carpeta public/
        fetch("get_mascota.php?id=" + id)
            .then(r => {
                if (!r.ok) {
                    throw new Error(`Error ${r.status}: No se pudo cargar la ficha.`);
                }
                return r.json();
            })
            .then(data => {
                if (!data || data.error || !data.nombre) {
                    throw new Error(data.error || "No se recibieron datos válidos.");
                }

                // 1. Rellenar Contenido General
                document.getElementById("modalTitulo").textContent = data.nombre;

                const descripcionElement = document.getElementById("modalDescripcion");
                if (data.descripcion) {
                    descripcionElement.innerHTML = data.descripcion.replace(/\n/g, '<br>');
                } else {
                    descripcionElement.textContent = 'Sin descripción.';
                }

                // Manejo de la imagen y corrección de ruta
                let img = "../assets/img/default_pet.jpg";
                if (data.url_foto && data.url_foto.includes("/uploads/")) {
                    img = "../uploads/" + data.url_foto.split("/uploads/")[1];
                } else if (data.url_foto) {
                    img = data.url_foto;
                }
                document.getElementById("modalImg").src = img;

                // 2. Rellenar la tabla
                document.getElementById("modalEspecie").textContent = data.nombre_especie;
                document.getElementById("modalRaza").textContent = data.nombre_raza ?? "Mestizo";
                document.getElementById("modalSexo").textContent = data.sexo;
                document.getElementById("modalEdad").textContent = data.edad ?? "No especificada";
                document.getElementById("modalTamano").textContent = data.tamano;
                document.getElementById("modalEstado").textContent = data.estado ?? "Disponible";
                document.getElementById("modalRefugio").textContent = data.nombre_refugio;

                // 3. Configurar Botón
                document.getElementById("modalAdoptarBtn").href = `../private/solicitar_adopcion.php?id=${data.id_mascota}`;

                // 4. Mostrar Modal
                document.getElementById("modalMascota").classList.add("open");
                document.getElementById("modalMascota").setAttribute("aria-hidden", "false");
            })
            .catch(error => {
                console.error("Error al cargar la ficha de la mascota:", error);
                alert("No se pudo cargar la información de la mascota.");
                document.body.style.overflow = '';
            });
    }

    function closeModal() {
        document.getElementById("modalMascota").classList.remove("open");
        document.getElementById("modalMascota").setAttribute("aria-hidden", "true");
        document.body.style.overflow = '';
    }

    // Cierre al hacer clic fuera del modal
    document.getElementById("modalMascota").addEventListener("click", function(e) {
        if (e.target === this) closeModal();
    });

    // Cierre con la tecla ESC
    document.addEventListener("keydown", e => {
        if (e.key === "Escape" && document.getElementById("modalMascota").classList.contains("open")) {
            closeModal();
        }
    });

    // ----------------------------------------------------
    // LÓGICA DE RECARGA AUTOMÁTICA CON POSICIÓN DE SCROLL
    // ----------------------------------------------------
    document.addEventListener("DOMContentLoaded", () => {
        const selectEspecie = document.getElementById('selectEspecie');
        const searchForm = document.getElementById('searchFilterForm');
        const selectRaza = searchForm.querySelector('select[name="raza"]');

        // 1. Manejo del Scroll
        // Si el hash es #filters después de la recarga, hace scroll hacia el formulario
        if (window.location.hash === '#filters') {
            // Se usa setTimeout para asegurar que todos los elementos se hayan renderizado
            setTimeout(() => {
                searchForm.scrollIntoView({
                    behavior: 'auto', // o 'smooth' si quieres una animación
                    block: 'start' // Mantiene el formulario en la parte superior visible
                });
                // Limpia el hash de la URL después del scroll para que no afecte la navegación normal
                history.replaceState({}, document.title, window.location.pathname + window.location.search);
            }, 0);
        }

        selectEspecie.addEventListener('change', function() {
            // Limpia la selección de raza
            selectRaza.value = "";

            // Agrega el hash al campo de acción del formulario antes de enviarlo
            searchForm.action = window.location.pathname + window.location.search.replace(/#.*$/, '') + '#filters';

            // Envía el formulario (recarga con filtro de especie)
            searchForm.submit();
        });
    });
</script>

<?php include '../includes/footer.php'; ?>