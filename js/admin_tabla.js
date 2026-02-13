function actualizarTabla() {
    const tabla = document.getElementById("tabla-registros");
    const fecha =
        document.getElementById("fecha")?.value ||
        new Date().toISOString().slice(0, 10);

    if (!tabla) return;

    fetch(`menu_administrador.php?ajax=tabla&fecha=${fecha}`)
        .then((res) => res.text())
        .then((html) => (tabla.innerHTML = html))
        .catch((err) => console.error("Error al actualizar tabla:", err));
}

document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("tabla-registros")) {
        actualizarTabla();
        setInterval(actualizarTabla, 5000);
    }
});

//Actualizar cuando hay cambio en input
const inputFecha = document.getElementById("fecha");

inputFecha.addEventListener("change", function () {
    actualizarTabla();
});

//Para exportar 
document.getElementById("btn-exportar").addEventListener("click", async function () {
    const fechaSeleccionada = document.getElementById("fecha").value;

    const popupFinal = await Swal.mixin({
        customClass: {
            confirmButton: "btn-confirmar",
            denyButton: "btn-denegar",
            cancelButton: "btn-cancelar",
            popup: "popup-container",
        },
    });

    const resultado = await popupFinal.fire({
        title: "¿Enviar por correo electrónico?",
        text: "Puedes descargar el archivo o enviarlo por correo.",
        icon: "question",
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: "Enviar por correo",
        denyButtonText: "Descargar",
        cancelButtonText: "Cancelar",
    });

    if (resultado.isConfirmed) {
        const { value: email } = await Swal.fire({
            title: "Introduce tu correo",
            icon: "info",
            input: "email",
            inputLabel: "Correo Electrónico",
            inputPlaceholder: "ejemplo@dominio.com",
            showCancelButton: true,
            confirmButtonText: "Enviar",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn-confirmar",
                cancelButton: "btn-cancelar",
                popup: "popup-container",
            },
        });

        if (email) {
            const formData = new FormData();
            formData.append("fecha", fechaSeleccionada);
            formData.append("correo", email);

            Swal.fire({
                title: "Cargando...",
                text: "Por favor espere mientras cargamos su correo",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })
            
            try {
                const res = await fetch("./php/exportar.php", {
                    method: "POST",
                    body: formData,
                });
                const text = await res.text();
                console.log("Respuesta del servidor:", text);

                popupFinal.fire({
                    title: "Correo enviado",
                    text: "El archivo ha sido enviado exitosamente.",
                    icon: "success",
                });
            } catch (err) {
                popupFinal.fire({
                    title: "Error",
                    text: "No se pudo enviar el correo.",
                    icon: "error",
                });
                console.error(err);
            }
        }

    } else if (resultado.isDenied) {
        // Descargar el archivo directamente
        try {
            const formData = new URLSearchParams();
            formData.append("fecha", fechaSeleccionada);

            const res = await fetch("./php/exportar.php", {
                method: "POST",
                body: formData,
            });

            if (!res.ok) throw new Error("Error en la descarga");

            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `registros_${fechaSeleccionada}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);

            popupFinal.fire({
                title: "Descargando...",
                text: "Se está descargando tu archivo.",
                icon: "success",
            });
        } catch (err) {
            popupFinal.fire({
                title: "Error",
                text: "No se pudo generar el archivo.",
                icon: "error",
            });
            console.error(err);
        }
    }
});