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

            <a href="#" class="logo">

                <i class="fa-solid fa-building"></i>

                <span>BUILDPRO</span>

            </a>

            <nav class="navbar">

                <a href="#inicio">Inicio</a>

                <a href="#nosotros">Nosotros</a>

                <a href="#servicios">Servicios</a>

                <a href="#estadisticas">Estadísticas</a>

                <a href="#galeria">Galería</a>

                <a href="#contacto">Contacto</a>

            </nav>

            <a href="vistas/login.php" class="btn-login">

                Iniciar sesión

            </a>

        </div>

    </header>

    <!--================ HERO ================-->

    <section class="hero" id="inicio">

        <div class="overlay"></div>

        <div class="container hero-content">

            <span class="subtitle">

                EMPRESA CONSTRUCTORA

            </span>

            <h1>

                Construimos el futuro con calidad, innovación y compromiso.

            </h1>

            <p>

                BuildPro integra tecnología y experiencia para gestionar
                obras, materiales, herramientas y personal desde una
                única plataforma moderna.

            </p>

            <div class="hero-buttons">

                <a href="login.php" class="btn-primary">

                    Iniciar sesión

                </a>

                <a href="#nosotros" class="btn-secondary">

                    Conocer más

                </a>

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

                    Somos una empresa especializada en la planificación,
                    ejecución y administración de obras civiles,
                    comerciales e industriales.

                </p>

                <p>

                    Nuestro compromiso es brindar soluciones de calidad,
                    utilizando procesos eficientes, personal altamente
                    capacitado y tecnología que optimiza cada etapa del
                    proyecto.

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

                        Construcción y ejecución de obras públicas,
                        privadas y residenciales.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-helmet-safety"></i>

                    <h3>

                        Gestión de Proyectos

                    </h3>

                    <p>

                        Planificación, seguimiento y control integral
                        de cada obra.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-truck"></i>

                    <h3>

                        Logística

                    </h3>

                    <p>

                        Administración eficiente de materiales,
                        herramientas y recursos.

                    </p>

                </div>

                <div class="service-card">

                    <i class="fa-solid fa-ruler-combined"></i>

                    <h3>

                        Diseño y Planificación

                    </h3>

                    <p>

                        Desarrollo de proyectos adaptados a las
                        necesidades de cada cliente.

                    </p>

                </div>

            </div>

        </div>

    </section>

        <!--================ ESTADÍSTICAS ================-->

    <section class="stats" id="estadisticas">

        <div class="container">

            <div class="section-header">

                <span>

                    BUILDPRO EN NÚMEROS

                </span>

                <h2>

                    Nuestra trayectoria nos respalda

                </h2>

            </div>

            <div class="stats-grid">

                <div class="stat-card">

                    <i class="fa-solid fa-building"></i>

                    <h3>

                        +250

                    </h3>

                    <p>

                        Obras Finalizadas

                    </p>

                </div>

                <div class="stat-card">

                    <i class="fa-solid fa-users"></i>

                    <h3>

                        +80

                    </h3>

                    <p>

                        Profesionales

                    </p>

                </div>

                <div class="stat-card">

                    <i class="fa-solid fa-calendar-check"></i>

                    <h3>

                        +20

                    </h3>

                    <p>

                        Años de Experiencia

                    </p>

                </div>

                <div class="stat-card">

                    <i class="fa-solid fa-award"></i>

                    <h3>

                        100%

                    </h3>

                    <p>

                        Compromiso

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