const config = {
    fps: 20,
    qrbox: 400,
    experimentalFeatures: {
        useBarCodeDetectorIfSupported: true,
    },
    verbose: true,
};

let qrReader = new Html5Qrcode("reader");
let isCameraRunning = false;

function iniciarLector() {
    if (isCameraRunning) return;
    document.getElementById("reader").style.display = "block";
    qrReader
        .start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            (errorMessage) => {
                console.warn(`Error de escaneo: ${errorMessage}`);
            }
        )
        .then(() => {
            isCameraRunning = true;
            document.getElementById("btnToggleLector").innerText =
                "⛔ Apagar lector";
        })
        .catch((err) => {
            console.error("No se pudo iniciar el lector:", err);
        });
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
                "🔄 Encender lector";
        })
        .catch((err) => {
            console.error("Error al detener lector:", err);
        });
}

let escaneando = false; // <-- variable para evitar múltiples procesos simultáneos

function onScanSuccess(decodedText, decodedResult) {
    if (escaneando) return; // Si ya estamos procesando, salimos
    escaneando = true;

    try {
        const usuario = JSON.parse(decodedText);

        fetch("registrar.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(usuario),
        })
            .then((res) => res.json())
            .then((data) => {
                const resultEl = document.getElementById("resultado");
                if (data.status === "ok") {
                    resultEl.innerText = `Registro de ${usuario.nombre} exitoso`;
                } else {
                    resultEl.innerText =
                        "Error: " + (data.message || "Error desconocido");
                }
            })
            .catch((error) => {
                document.getElementById("resultado").innerText =
                    "Error en la conexión al servidor";
                console.error("Fetch error:", error);
            })
            .finally(() => {
                // Permitir nuevo escaneo después de 2 segundos
                setTimeout(() => {
                    escaneando = false;
                }, 2000);
            });
    } catch (e) {
        document.getElementById("resultado").innerText =
            "QR inválido (no es JSON)";
        escaneando = false; // desbloqueamos para siguiente intento
    }
}

document.getElementById("btnToggleLector").addEventListener("click", () => {
    if (isCameraRunning) {
        detenerLector();
    } else {
        iniciarLector();
    }
});
