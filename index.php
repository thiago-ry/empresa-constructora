<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: vistas/dashboard/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BuildPro | Empresa Constructora</title>

    <link rel="stylesheet" href="assets/css/landing.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

    <!--================ HEADER ================-->

<header class="header">

    <div class="container">

        <a href="#inicio" class="logo">
            <img src="assets/img/logo.png" alt="BuildPro">
        </a>

        <nav class="navbar">

            <a href="#inicio">Inicio</a>

            <a href="#nosotros">Quiénes Somos</a>

            <a href="#servicios">Servicios</a>

            <a href="#ventajas">Ventajas</a>

            <a href="#galeria">Proyectos</a>

            <a href="#contacto">Contacto</a>

        </nav>

      <a href="vistas/login.php" class="btn-login">

            Iniciar sesión

        </a>
        </div>

    </div>

</header>

    <!--================ HERO ================-->

    <section class="hero" id="inicio">

        <div class="overlay"></div>

        <div class="hero-machine">

            <img
                src="https://static.vecteezy.com/system/resources/thumbnails/050/594/564/small_2x/excavator-truck-side-view-full-length-isolate-on-transparency-background-png.png"
                alt="Excavadora"
                id="excavadora"
                class="excavadora">

        </div>

        <div class="container hero-grid"">

            <div class=" hero-content">
            <br>

            <h1>
                Construimos proyectos que perduran en el tiempo.
            </h1>

            <p>
                Somos una empresa constructora especializada en obras
                civiles, comerciales y residenciales.
                Transformamos ideas en proyectos sólidos, seguros y de
                calidad, acompañando a nuestros clientes en cada etapa
                de la construcción.
            </p>

            <div class="hero-buttons">
                <a href="#contacto" class="btn-primary">
                    Solicitar presupuesto
                </a>

                <a href="#proyectos" class="btn-secondary">
                    Ver proyectos
                </a>
            </div>

        </div>

        </div>

    </section>

    <!--================ NOSOTROS ================-->

    <section class="about" id="nosotros">

        <div class="container about-grid">

            <div class="about-image">

                <img src="assets/img/about.jpg" alt="Nosotros">

            </div>

            <div class="about-content">

                <span class="section-title">

                    QUIÉNES SOMOS

                </span>

                <h2>

                    Construimos proyectos que dejan huella.

                </h2>
                <p>
                    Somos una empresa constructora comprometida con la
                    excelencia en cada proyecto que desarrollamos.
                </p>

                <p>
                    Contamos con un equipo multidisciplinario de
                    profesionales capacitados para llevar adelante obras
                    residenciales, comerciales e industriales,
                    garantizando calidad, seguridad y cumplimiento en
                    cada etapa del proceso.
                </p>

                <p>
                    Nuestro objetivo es convertir las ideas de nuestros
                    clientes en espacios funcionales, modernos y
                    duraderos que generen valor a largo plazo.
                </p>
                <a href="#servicios" class="btn-primary">

                    Nuestros servicios

                </a>

            </div>

        </div>

    </section>

    <!--================ SERVICIOS ================-->

    <section class="services" id="servicios">

        <div class="container">

            <div class="section-header">

                <span>

                    SERVICIOS

                </span>

                <h2>

                    Soluciones para cada proyecto

                </h2>

            </div>

            <div class="services-grid">

                <div class="service-card">

                    <i class="fa-solid fa-building"></i>

                    <h3>

                        Obras Civiles

                    </h3>

                    <p>
                        Construcción de viviendas, edificios y obras de
                        infraestructura con altos estándares de calidad.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-store"></i>

                    <h3>
                        Obras comerciales
                    </h3>

                    <p>
                        Desarrollo de locales comerciales, oficinas y espacios
                        corporativos adaptados a cada necesidad.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-home"></i>

                    <h3>

                        Remodelaciones

                    </h3>

                    <p>

                        Renovación y ampliación de espacios existentes,
                        optimizando funcionalidad y diseño.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-hard-hat"></i>

                    <h3>

                        Obras inndustriales

                    </h3>

                    <p>

                        Construcción de depósitos, plantas industriales y
                        estructuras de gran escala.

                    </p>

                </div>

            </div>

    </section>

    <section class="services" id="ventajas">

        <div class="container">

            <div class="section-header">

                <span>POR QUÉ ELEGIRNOS</span>

                <h2>
                    La confianza de nuestros clientes nos respalda
                </h2>

            </div>

            <div class="services-grid">

                <div class="service-card">
                    <i class="fa-solid fa-medal"></i>
                    <h3>Calidad Garantizada</h3>
                    <p>
                        Utilizamos materiales y procesos que aseguran
                        resultados duraderos.
                    </p>
                </div>

                <div class="service-card">
                    <i class="fa-solid fa-users"></i>
                    <h3>Equipo Profesional</h3>
                    <p>
                        Personal especializado en cada etapa de la obra.
                    </p>
                </div>

                <div class="service-card">
                    <i class="fa-solid fa-clock"></i>
                    <h3>Cumplimiento</h3>
                    <p>
                        Respetamos plazos y mantenemos una comunicación
                        constante con nuestros clientes.
                    </p>
                </div>

                <div class="service-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Seguridad</h3>
                    <p>
                        Aplicamos protocolos de seguridad para proteger
                        a nuestro personal y a cada proyecto.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!--================ GALERÍA ================-->

    <section class="gallery" id="galeria">

        <div class="container">

            <div class="section-header">

                <span>

                    PROYECTOS

                </span>

                <h2>

                    Algunos de nuestros trabajos

                </h2>

            </div>

            <div class="gallery-grid">

                <div class="gallery-item">

                    <img src="assets/img/proyecto1.jpg" alt="Proyecto 1">

                </div>

                <div class="gallery-item">

                    <img src="assets/img/proyecto2.jpg" alt="Proyecto 2">

                </div>

                <div class="gallery-item">

                    <img src="assets/img/proyecto3.jpg" alt="Proyecto 3">

                </div>

                <div class="gallery-item">

                    <img src="assets/img/proyecto4.jpg" alt="Proyecto 4">

                </div>

                <div class="gallery-item">

                    <img src="assets/img/proyecto5.jpg" alt="Proyecto 5">

                </div>

                <div class="gallery-item">

                    <img src="assets/img/proyecto6.jpg" alt="Proyecto 6">

                </div>

            </div>

        </div>

    </section>

    <!--================ CONTACTO ================-->

    <section class="contact" id="contacto">

        <div class="container">

            <div class="section-header">

                <span>

                    CONTACTO

                </span>

                <h2>

                    Estamos listos para construir contigo

                </h2>

            </div>

            <div class="contact-grid">

                <div class="contact-card">

                    <i class="fa-solid fa-location-dot"></i>

                    <h3>

                        Dirección

                    </h3>

                    <p>

                        Formosa, Argentina

                    </p>

                </div>

                <div class="contact-card">

                    <i class="fa-solid fa-phone"></i>

                    <h3>

                        Teléfono

                    </h3>

                    <p>

                        +54 370 XXX-XXXX

                    </p>

                </div>

                <div class="contact-card">

                    <i class="fa-solid fa-envelope"></i>

                    <h3>

                        Correo

                    </h3>

                    <p>

                        contacto@buildpro.com

                    </p>

                </div>

                <div class="contact-card">

                    <i class="fa-solid fa-clock"></i>

                    <h3>

                        Horarios

                    </h3>

                    <p>

                        Lunes a Viernes<br>

                        08:00 - 18:00

                    </p>

                </div>

            </div>

        </div>

    </section>
    <!--================ FOOTER ================-->

    <footer class="footer">

        <div class="container">

            <div class="footer-grid">

                <div class="footer-col">

                    <h3>

                        <i class="fa-solid fa-building"></i>

                        BUILDPRO

                    </h3>

                    <p>

                        Empresa especializada en la planificación,
                        ejecución y gestión de proyectos de construcción,
                        comprometida con la calidad, la seguridad y la
                        innovación.

                    </p>

                </div>

                <div class="footer-col">

                    <h4>

                        Navegación

                    </h4>

                    <ul>

                        <li>

                            <a href="#inicio">

                                Inicio

                            </a>

                        </li>

                        <li>

                            <a href="#nosotros">

                                Nosotros

                            </a>

                        </li>

                        <li>

                            <a href="#servicios">

                                Servicios

                            </a>

                        </li>

                        <li>

                            <a href="#galeria">

                                Galería

                            </a>

                        </li>

                        <li>

                            <a href="#contacto">

                                Contacto

                            </a>

                        </li>

                    </ul>

                </div>

                <div class="footer-col">

                    <h4>

                        Servicios

                    </h4>

                    <ul>

                        <li>

                            Obras Civiles

                        </li>

                        <li>

                            Gestión de Proyectos

                        </li>

                        <li>

                            Logística

                        </li>

                        <li>

                            Diseño y Planificación

                        </li>

                    </ul>

                </div>

                <div class="footer-col">

                    <h4>

                        Síguenos

                    </h4>

                    <div class="social-links">

                        <a href="#">

                            <i class="fab fa-facebook-f"></i>

                        </a>

                        <a href="#">

                            <i class="fab fa-instagram"></i>

                        </a>

                        <a href="#">

                            <i class="fab fa-linkedin-in"></i>

                        </a>

                        <a href="#">

                            <i class="fab fa-youtube"></i>

                        </a>

                    </div>

                </div>

            </div>

            <div class="footer-bottom">

                <p>

                    © 2026 BuildPro. Todos los derechos reservados.

                </p>

            </div>

        </div>

    </footer>

    <!--================ BOTÓN VOLVER ARRIBA ================-->

    <a href="#inicio" class="back-top">

        <i class="fa-solid fa-arrow-up"></i>

    </a>

    <!--================ SCRIPTS ================-->

    <script src="assets/js/landing.js"></script>

</body>

</html>