//==============================
// Navbar al hacer scroll
//==============================

const header = document.querySelector(".header");

window.addEventListener("scroll", () => {

    if (window.scrollY > 80) {

        header.style.background = "rgba(7,13,20,.95)";
        header.style.boxShadow = "0 10px 30px rgba(0,0,0,.35)";

    } else {

        header.style.background = "rgba(7,13,20,.65)";
        header.style.boxShadow = "none";

    }

});

//==============================
// Animaciones al aparecer
//==============================

const elementos = document.querySelectorAll(

    ".service-card, .stat-card, .gallery-item, .contact-card"

);

const observer = new IntersectionObserver((entries) => {

    entries.forEach((entry) => {

        if (entry.isIntersecting) {

            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";

        }

    });

}, {

    threshold: .15

});

elementos.forEach((item) => {

    item.style.opacity = "0";
    item.style.transform = "translateY(40px)";
    item.style.transition = ".6s ease";

    observer.observe(item);

});

//==============================
// Botón volver arriba
//==============================

const backTop = document.querySelector(".back-top");

window.addEventListener("scroll", () => {

    if (window.scrollY > 400) {

        backTop.style.opacity = "1";
        backTop.style.pointerEvents = "all";

    } else {

        backTop.style.opacity = "0";
        backTop.style.pointerEvents = "none";

    }

});

//==============================
// Scroll suave
//==============================

document.querySelectorAll('a[href^="#"]').forEach(link => {

    link.addEventListener("click", function(e){

        e.preventDefault();

        const destino = document.querySelector(this.getAttribute("href"));

        destino.scrollIntoView({

            behavior: "smooth"

        });

    });

});

//==============================
// Excavadora Hero
//==============================

const excavadora = document.getElementById("excavadora");

function animarExcavadora(){

    if(!excavadora) return;

    const hero = document.querySelector(".hero");

    const heroHeight = hero.offsetHeight;

    const scroll = Math.min(window.scrollY, heroHeight);

    const progreso = scroll / heroHeight;

    const anchoExcavadora = excavadora.offsetWidth;

    // Comienza visible a la derecha

    const inicio =
        window.innerWidth -
        anchoExcavadora -
        30;

    // Sale completamente por la izquierda

    const fin =
        -anchoExcavadora;

    const posicion =
        inicio +
        ((fin - inicio) * progreso);

    excavadora.style.left =
        posicion + "px";

    excavadora.style.transform =
        `scaleX(-1) translateY(${Math.sin(scroll * 0.01) * 5}px)`;

    excavadora.style.opacity =
        0.25 - (progreso * 0.15);

}

window.addEventListener(
    "scroll",
    animarExcavadora
);

window.addEventListener(
    "resize",
    animarExcavadora
);

// Ejecutar al cargar para evitar el salto

animarExcavadora();