<?php

session_start();

require_once __DIR__ . "/controladores/LandingPageController.php";

$landingPageController = new LandingPageController();

if (isset($_SESSION['usuario'])) {
    header("Location: vistas/dashboard/index.php");
    exit;
}

$proyectos = $landingPageController->obtenerTodos();

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

    <header class="header">

        <div class="container">

            <a href="#inicio" class="logo">
                <img src="assets/img/logo.png" alt="BuildPro">
            </a>

            <nav class="navbar">

                <a href="#inicio">Inicio</a>

                <a href="#nosotros">Quiénes Somos</a>

                <a href="#servicios">Servicios</a>

                <a href="#ubicacion">Ubicación</a>

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
                <a href="https://wa.me/5493704753338?text=Hola%20BUILDPRO,%20quiero%20hacer%20una%20consulta."
                    target="_blank"
                    class="btn-primary">
                    <i class="fa-brands fa-whatsapp"></i>

                    ⠀Solicitar presupuesto
                </a>

                <a href="#galeria" class="btn-secondary">
                    Ver proyectos
                </a>

            </div>

        </div>

        </div>

    </section>

    <section class="about" id="nosotros">

        <div class="container about-grid">

            <div class="about-image">

                <img src="https://tse3.mm.bing.net/th/id/OIP.hSYzJYTVcu1iqD1sPHGCCAHaE8?r=0&rs=1&pid=ImgDetMain&o=7&rm=3" alt="Nosotros">

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
            </div>

        </div>

    </section>

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

    <section class="ubicacion" id="ubicacion">

        <div class="container">

            <div class="section-header">
                <span>UBICACIÓN</span>
                <h2>¿Dónde nos encontramos?</h2>
            </div>

            <div class="ubicacion-grid">

                <div class="map-container">

                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3003.3033171282714!2d-58.159272525393966!3d-26.145262877112945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x945ca5a793027157%3A0x4258abecc260dc62!2sEPET%20N%C2%B07%20%22Arcadio%20Salemi%22!5e1!3m2!1ses-419!2sar!4v1790942685087!5m2!1ses-419!2sar"
                        width="100%"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin">
                    </iframe>

                </div>

                <!-- DESCRIPCIÓN A LA DERECHA -->
                <div class="ubicacion-info">

                    <span class="section-title">NUESTRA UBICACIÓN</span>

                    <h2>
                        Estamos en Formosa
                    </h2>

                    <p>
                        Nuestra empresa se encuentra ubicada en la ciudad de
                        Formosa, Argentina, en una zona de fácil acceso para
                        nuestros clientes y colaboradores.
                    </p>

                    <div class="ubicacion-dato">
                        <i class="fa-solid fa-location-dot"></i>

                        <div>
                            <h3>Dirección</h3>
                            <p>
                                Av. Raúl Alfonsín 1612-1698,
                                P3600JQA Formosa, Argentina.
                            </p>
                        </div>
                    </div>

                    <div class="ubicacion-dato">
                        <i class="fa-solid fa-clock"></i>

                        <div>
                            <h3>Horario de atención</h3>
                            <p>
                                Lunes a Viernes<br>
                                08:00 - 18:00
                            </p>
                        </div>
                    </div>

                    <a
                        href="https://www.google.com/maps/search/?api=1&query=Av.+Raúl+Alfonsín+1612-1698,+Formosa,+Argentina"
                        target="_blank"
                        class="btn-primary">
                        <i class="fa-solid fa-map-location-dot"></i>
                        Ver en Google Maps
                    </a>

                </div>

            </div>

        </div>

    </section>

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
                <?php if (!empty($proyectos)): ?> <?php foreach ($proyectos as $proyecto): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($proyecto['ruta_imagen']); ?>" alt="<?php echo htmlspecialchars($proyecto['descripcion'] ?? 'Proyecto BuildPro'); ?>">
                        </div> <?php endforeach; ?> <?php else: ?> <div class="gallery-empty">
                        <p>Actualmente no hay proyectos finalizados para mostrar.</p>
                    </div> <?php endif; ?>
            </div>

        </div>

    </section>

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

                        <a
                            href="https://www.google.com/maps/search/?api=1&query=Av.+Raúl+Alfonsín+1612-1698,+Formosa,+Argentina"
                            target="_blank"
                            style="color: #ffb703; text-decoration: none;">
                            Av. Raúl Alfonsín, Formosa, Argentina
                        </a>

                    </p>

                </div>

                <div class="contact-card">

                    <i class="fa-solid fa-phone"></i>

                    <h3>

                        Teléfono

                    </h3>

                    <p>

                        +54 370 475-3338

                    </p>

                </div>

                <div class="contact-card">

                    <i class="fa-solid fa-envelope"></i>

                    <h3>

                        Correo

                    </h3>

                    <p>

                        rohaly1310thiago@gmail.com

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

    <a href="#inicio" class="back-top">

        <i class="fa-solid fa-arrow-up"></i>

    </a>

    <script src="assets/js/landing.js"></script>

</body>

</html>