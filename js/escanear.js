const qrReader = new Html5Qrcode("reader");
let isCameraRunning = false;
let escaneando = false;
let buffer = "";
let inputTimer = null;

const config = {
    fps: 20,
    qrbox: 400,
    experimentalFeatures: { useBarCodeDetectorIfSupported: true },
    verbose: false,
};

function iniciarLector() {
    if (isCameraRunning) return;

    document.getElementById("reader").style.display = "block";
    qrReader
        .start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            console.warn
        )
        .then(() => {
            isCameraRunning = true;
            document.getElementById("btnToggleLector").innerText =
                "Apagar camara web";
        })
        .catch((err) => console.error("No se pudo iniciar el lector:", err));
}

function detenerLector() {
    if (!isCameraRunning) return;

    qrReader
        .stop()
        .then(() => {
            qrReader.clear();
            isCameraRunning = false;
            document.getElementById("reader").style.display = "none";
            document.getElementById("btnToggleLector").innerText =
                "Encender lector desde camara web";
        })
        .catch((err) => console.error("Error al detener lector:", err));
}

function onScanSuccess(decodedText) {
    if (escaneando) return;
    escaneando = true;

    const campos = decodedText
        .split("|")
        .map((c) => c.trim())
        .filter(Boolean);
    if (campos.length !== 5) {
        mostrarMensaje("QR inválido (faltan datos)");
        escaneando = false;
        return;
    }

    const usuario = {
        nombre: campos[0],
        apellido_paterno: campos[1],
        apellido_materno: campos[2],
        matricula: campos[3],
        carrera: campos[4],
    };

    fetch("php/registrar.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(usuario),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "ok") {
                mostrarMensaje(`✅ Registro de ${usuario.nombre} exitoso`);
            } else {
                mostrarMensaje(
                    `❌ Error: ${data.message || "Error desconocido"}`
                );
            }
        })
        .catch(() => mostrarMensaje("❌ Error en conexión con servidor"))
        .finally(() =>
            setTimeout(() => {
                escaneando = false;
                document.getElementById("scanner-input").focus();
            }, 2000)
        );
}

function mostrarMensaje(mensaje) {
    document.getElementById("resultado").innerText = mensaje;
    setTimeout(()=> {
        resultado.innerText = "";
    }, 2000);
}

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("scanner-input");
    input?.focus();

    input?.addEventListener("input", () => {
        clearTimeout(inputTimer);
        buffer = input.value;
        inputTimer = setTimeout(() => {
            if (buffer.length > 10) {
                onScanSuccess(buffer);
                input.value = "";
                buffer = "";
            }
        }, 300);
    });

    ["click", "keydown"].forEach((evt) => {
        window.addEventListener(evt, () => input?.focus());
    });

    document
        .getElementById("btnToggleLector")
        ?.addEventListener("click", () => {
            isCameraRunning ? detenerLector() : iniciarLector();
        });
});

const lectorWrapper = document.getElementById("lector-wrapper");
const btnToggle = document.getElementById("btnToggleLector");
const reader = document.getElementById("reader");
let lectorActivo = false;

btnToggle.addEventListener("click", () => {
    lectorActivo = !lectorActivo;

    if (lectorActivo) {
        lectorWrapper.classList.remove("lector-inactivo");
        lectorWrapper.classList.add("lector-activo");
        reader.style.display = "block";
        btnToggle.textContent = "Apagar camara web";
    } else {
        lectorWrapper.classList.remove("lector-activo");
        lectorWrapper.classList.add("lector-inactivo");
        reader.style.display = "none";
        btnToggle.textContent = "Encender lector desde camara web";
    }
});