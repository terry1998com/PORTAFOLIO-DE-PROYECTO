//Animacion del navbar
let lastScrollTop = 0;
const navbar = document.getElementById("navbar");

window.addEventListener("scroll", () => {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
        // Bajando: ocultar navbar
        navbar.classList.add("hide");
    } else {
        // Subiendo: mostrar navbar
        navbar.classList.remove("hide");
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // para evitar valores negativos
});
