document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("qr-form");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const dato1 = document.getElementById("nombre").value.trim();
        const dato2 = document.getElementById("aPaterno").value.trim();
        const dato3 = document.getElementById("aMaterno").value.trim();
        const dato4 = document.getElementById("matricula").value.trim();
        const dato5 = document.getElementById("carrera").value.trim();

        const qrData = `${dato1}|${dato2}|${dato3}|${dato4}|${dato5}`;

        const matricula = dato4;

        const qrCode = new QRCodeStyling({
            width: 250,
            height: 250,
            data: qrData,
            dotsOptions: {
                color: "#000000",
                type: "rounded",
            },
            backgroundOptions: {
                color: "#ffffff",
            },
            imageOptions: {
                crossOrigin: "anonymous",
                margin: 20,
            },
        });

        qrCode.getRawData("png").then((blob) => {
            const reader = new FileReader();

            reader.onloadend = function () {
                const base64data = reader.result;

                fetch("./php/guardar_qr.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body:
                        "image=" +
                        encodeURIComponent(base64data) +
                        "&matricula=" +
                        encodeURIComponent(matricula),
                })
                    .then((res) => res.json())
                    .then(async (data) => {
                        if (data.status === "ok") {
                            const img = document.createElement("img");
                            img.src = `./temp/${data.filename}`;
                            img.alt = "QR generado";
                            img.style.marginTop = "20px";
                            img.style.width = "250px";

                            const mostrarQR = await Swal.mixin({
                                customClass: {
                                    confirmButton: "btn-confirmar",
                                    denyButton: "btn-denegar",
                                    cancelButton: "btn-cancelar",
                                },
                            });
                            const resultado = await mostrarQR.fire({
                                title: "Imagen Generada",
                                text: "Puede elejir una de las opciones siguientes: ",
                                imageUrl: `./temp/${data.filename}`,
                                imageWithd: 200,
                                imageHeight: 200,
                                imageAlt: "QR Generado",
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: "Enviar por correo",
                                denyButtonText: "Descargar QR",
                                cancelButtonText: "Cancelar",
                            });
                            if (resultado.isConfirmed) {
                                const { value: email } = await Swal.fire({
                                    title: "Introduce tu correo",
                                    icon: "info",
                                    input: "email",
                                    inputLabel: "Correo Electronico",
                                    inputPlaceholder: "ejemplo@dominio.com",
                                    showCancelButton: true,
                                    showDenyButton: false,
                                    confirmButtonText: "Enviar",
                                    cancelButtonText: "Cancelar",
                                    customClass: {
                                        confirmButton: "btn-confirmar",
                                        cancelButton: "btn-cancelar",
                                        popup: "popup-container",
                                    },
                                });

                                if (email) {
                                    // Mostrar carga antes de enviar
                                    Swal.fire({
                                        title: "Enviando correo...",
                                        text: "Por favor espera",
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        },
                                    });

                                    fetch("./php/enviar_qr.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type":
                                                "application/x-www-form-urlencoded",
                                        },
                                        body:
                                            "email=" +
                                            encodeURIComponent(email) +
                                            "&filename=" +
                                            encodeURIComponent(data.filename) +
                                            "&nombre=" +
                                            encodeURIComponent(dato1) +
                                            "&matricula=" +
                                            encodeURIComponent(matricula),
                                    })
                                        .then((res) => res.json())
                                        .then((respuesta) => {
                                            Swal.close(); // Cerrar alerta de carga

                                            if (respuesta.status === "ok") {
                                                Swal.fire({
                                                    title: "Correo enviado",
                                                    text: "Se envió el QR al correo electrónico.",
                                                    icon: "success",
                                                    customClass: {
                                                        confirmButton:
                                                            "btn-confirmar",
                                                    },
                                                });
                                            } else {
                                                Swal.fire(
                                                    "Error",
                                                    respuesta.message,
                                                    "error"
                                                );
                                            }
                                        })
                                        .catch((error) => {
                                            Swal.close();
                                            Swal.fire(
                                                "Error",
                                                "Ocurrió un problema al enviar el correo.",
                                                "error"
                                            );
                                        });
                                }
                            } else if (resultado.isDenied) {
                                if (qrCode) {
                                    qrCode.download({
                                        name: matricula,
                                        extension: "png",
                                    });
                                }
                                mostrarQR.fire({
                                    title: "Descargando QR...",
                                    text: "Se iniciará la descarga de tu QR.",
                                    icon: "success",
                                });
                            }
                        } else {
                            Swal.fire("Error", data.message, "error");
                        }
                    });
            };
            reader.readAsDataURL(blob);
        });
    });
});
