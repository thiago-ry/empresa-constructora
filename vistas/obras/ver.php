
<?php

require_once "../../modelos/Obra.php";
require_once "../../modelos/Etapa.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();

$id = $_GET["id"] ?? 0;

$detalle = $obra->buscarPorId($id);

if (!$detalle) {
    header("Location: index.php");
    exit;
}

$etapa = new Etapa();

$etapas = $etapa->obtenerPorObra($id);
$avance = $etapa->calcularAvance($id);
$resumen = $etapa->obtenerResumen($id);

$avance = max(0, min(100, (float) $avance));

$estado = $detalle["estado"] ?? "Sin estado";

$estadoClase = "estado-default";

switch ($estado) {
    case "En Proceso":
        $estadoClase = "estado-proceso";
        break;

    case "Finalizada":
        $estadoClase = "estado-finalizada";
        break;

    case "Suspendida":
        $estadoClase = "estado-suspendida";
        break;

    case "Cancelada":
        $estadoClase = "estado-cancelada";
        break;

    case "Planificacion":
    case "Planificación":
        $estadoClase = "estado-planificacion";
        break;
}

$nombreCliente = trim(
    ($detalle["nombre_cliente"] ?? "") . " " .
    ($detalle["apellido_cliente"] ?? "")
);

$nombreJefe = trim(
    ($detalle["nombre_jefe_obra"] ?? "") . " " .
    ($detalle["apellido_jefe_obra"] ?? "")
);

$idObra = (int) $detalle["id_obra"];

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<style>

    /* =========================================
       DETALLE DE OBRA
    ========================================= */

    .obra-page {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        padding-bottom: 35px;
        color: var(--text-primary, #f1f5f9);
    }

    .obra-page * {
        box-sizing: border-box;
    }

    /* ENCABEZADO */

    .obra-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .obra-header-info {
        min-width: 0;
    }

    .obra-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8b9aab;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .obra-breadcrumb i {
        font-size: 11px;
    }

    .obra-title {
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.8px;
        margin: 0;
        color: #f8fafc;
        overflow-wrap: anywhere;
    }

    .obra-subtitle {
        margin: 8px 0 0;
        color: #91a0b2;
        font-size: 14px;
        line-height: 1.6;
    }

    .obra-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 11px 17px;
        border-radius: 11px;
        background: #1a2736;
        border: 1px solid #2c3b4d;
        color: #dce5ef;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: .2s ease;
        white-space: nowrap;
    }

    .obra-back:hover {
        background: #253548;
        border-color: #f4b400;
        color: #f4b400;
        transform: translateY(-2px);
    }

    /* INFORMACIÓN PRINCIPAL */

    .obra-main-card {
        background: linear-gradient(145deg, #152334, #101b29);
        border: 1px solid #26374a;
        border-radius: 19px;
        padding: 25px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }

    .obra-main-card::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(244, 180, 0, .035);
        right: -100px;
        top: -130px;
        pointer-events: none;
    }

    .obra-main-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .obra-section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .obra-section-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(244, 180, 0, .12);
        color: #f4b400;
        font-size: 17px;
    }

    .obra-section-title h2 {
        font-size: 17px;
        margin: 0;
        font-weight: 750;
        color: #f8fafc;
    }

    .obra-section-title p {
        font-size: 12px;
        color: #8fa0b3;
        margin: 4px 0 0;
    }

    .obra-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
        border: 1px solid transparent;
    }

    .obra-status::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .estado-proceso {
        color: #60a5fa;
        background: rgba(59, 130, 246, .12);
        border-color: rgba(59, 130, 246, .2);
    }

    .estado-finalizada {
        color: #4ade80;
        background: rgba(34, 197, 94, .12);
        border-color: rgba(34, 197, 94, .2);
    }

    .estado-suspendida {
        color: #fbbf24;
        background: rgba(245, 158, 11, .12);
        border-color: rgba(245, 158, 11, .2);
    }

    .estado-cancelada {
        color: #f87171;
        background: rgba(239, 68, 68, .12);
        border-color: rgba(239, 68, 68, .2);
    }

    .estado-planificacion,
    .estado-default {
        color: #c4b5fd;
        background: rgba(139, 92, 246, .12);
        border-color: rgba(139, 92, 246, .2);
    }

    /* DATOS */

    .obra-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
    }

    .obra-info-item {
        padding: 17px;
        background: rgba(255, 255, 255, .025);
        border: 1px solid #29394b;
        border-radius: 13px;
        min-width: 0;
        transition: .2s ease;
    }

    .obra-info-item:hover {
        background: rgba(255, 255, 255, .045);
        border-color: #46576b;
    }

    .obra-info-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8fa0b3;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 11px;
    }

    .obra-info-label i {
        color: #f4b400;
        font-size: 13px;
    }

    .obra-info-value {
        display: block;
        color: #f1f5f9;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .obra-info-empty {
        color: #75869a;
        font-style: italic;
        font-weight: 500;
    }

    .obra-description {
        margin-top: 15px;
        padding: 19px;
        border: 1px solid #29394b;
        background: rgba(255, 255, 255, .025);
        border-radius: 13px;
    }

    .obra-description p {
        margin: 10px 0 0;
        color: #b4c0ce;
        font-size: 13px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    /* ENCABEZADOS DE SECCIÓN */

    .obra-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin: 30px 0 17px;
    }

    .obra-section-header h2 {
        color: #f8fafc;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }

    .obra-section-header p {
        margin: 5px 0 0;
        font-size: 12px;
        color: #8495a8;
    }

    .obra-section-line {
        flex: 1;
        height: 1px;
        background: #273647;
        margin-left: 15px;
    }

    /* RESUMEN */

    .obra-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
    }

    .obra-stat-card {
        background: #152334;
        border: 1px solid #29394b;
        border-radius: 15px;
        padding: 19px;
        min-width: 0;
        transition: .25s ease;
    }

    .obra-stat-card:hover {
        transform: translateY(-4px);
        border-color: #526176;
    }

    .obra-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 17px;
    }

    .obra-stat-icon {
        width: 39px;
        height: 39px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 11px;
        background: rgba(244, 180, 0, .12);
        color: #f4b400;
        font-size: 16px;
    }

    .obra-stat-number {
        color: #f8fafc;
        font-size: 29px;
        font-weight: 850;
        line-height: 1;
        margin: 0;
    }

    .obra-stat-label {
        color: #91a0b2;
        font-size: 12px;
        font-weight: 650;
        margin: 9px 0 0;
    }

    .stat-finalizadas .obra-stat-icon {
        color: #4ade80;
        background: rgba(34, 197, 94, .12);
    }

    .stat-proceso .obra-stat-icon {
        color: #60a5fa;
        background: rgba(59, 130, 246, .12);
    }

    .stat-pendientes .obra-stat-icon {
        color: #fbbf24;
        background: rgba(245, 158, 11, .12);
    }

    .stat-avance {
        border-color: rgba(244, 180, 0, .3);
        background: linear-gradient(145deg, #1c2c3c, #152334);
    }

    .stat-avance .obra-stat-number {
        color: #f4b400;
    }

    /* PROGRESO */

    .obra-progress {
        width: 100%;
        height: 7px;
        background: #29394b;
        border-radius: 20px;
        overflow: hidden;
        margin-top: 17px;
    }

    .obra-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #e7a900, #ffcf40);
        border-radius: inherit;
        transition: width .5s ease;
    }

    /* MÓDULOS */

    .obra-modules-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
    }

    .obra-module-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        padding: 21px;
        background: #152334;
        border: 1px solid #29394b;
        border-radius: 16px;
        text-decoration: none;
        color: inherit;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .obra-module-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(244, 180, 0, .035);
        right: -40px;
        bottom: -40px;
        transition: .25s ease;
    }

    .obra-module-card:hover {
        transform: translateY(-5px);
        border-color: rgba(244, 180, 0, .55);
        background: #192a3d;
        color: inherit;
    }

    .obra-module-card:hover::after {
        transform: scale(1.6);
    }

    .obra-module-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 47px;
        height: 47px;
        border-radius: 13px;
        background: rgba(244, 180, 0, .12);
        color: #f4b400;
        font-size: 19px;
        margin-bottom: 17px;
        transition: .25s ease;
    }

    .obra-module-card:hover .obra-module-icon {
        background: #f4b400;
        color: #101b29;
        transform: scale(1.05);
    }

    .obra-module-title {
        color: #f1f5f9;
        font-size: 14px;
        font-weight: 800;
        margin: 0 0 7px;
    }

    .obra-module-description {
        color: #8fa0b3;
        font-size: 11px;
        line-height: 1.6;
        margin: 0 0 19px;
        min-height: 35px;
    }

    .obra-module-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        gap: 8px;
        padding-top: 13px;
        border-top: 1px solid #2a3a4d;
        color: #f4b400;
        font-size: 12px;
        font-weight: 800;
        margin-top: auto;
    }

    .obra-module-action i {
        transition: transform .2s ease;
    }

    .obra-module-card:hover .obra-module-action i {
        transform: translateX(5px);
    }

    /* RESPONSIVE */

    @media (max-width: 1200px) {

        .obra-stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .obra-modules-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

    }

    @media (max-width: 850px) {

        .obra-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .obra-modules-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .obra-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

    }

    @media (max-width: 550px) {

        .obra-page {
            padding: 0 0 25px;
        }

        .obra-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 15px;
        }

        .obra-back {
            width: 100%;
        }

        .obra-main-card {
            padding: 17px;
            border-radius: 15px;
        }

        .obra-main-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .obra-info-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .obra-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .obra-stat-card {
            padding: 15px;
        }

        .obra-stat-number {
            font-size: 25px;
        }

        .obra-modules-grid {
            grid-template-columns: 1fr;
            gap: 11px;
        }

        .obra-module-card {
            padding: 17px;
        }

        .obra-section-header {
            margin-top: 25px;
        }

        .obra-section-header h2 {
            font-size: 16px;
        }

        .obra-section-line {
            display: none;
        }

    }

</style>

<main class="content">

    <div class="obra-page">

        <!-- ENCABEZADO -->

        <header class="obra-header">

            <div class="obra-header-info">

                <div class="obra-breadcrumb">
                    <i class="fa-solid fa-house"></i>
                    <span>Obras</span>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span>Detalle de obra</span>
                </div>

                <h1 class="obra-title">
                    <?= htmlspecialchars($detalle["nombre_obra"]) ?>
                </h1>

                <p class="obra-subtitle">
                    Panel de control y seguimiento del proyecto.
                </p>

            </div>

            <a href="index.php" class="obra-back">
                <i class="fa-solid fa-arrow-left"></i>
                Volver a obras
            </a>

        </header>


        <!-- INFORMACIÓN GENERAL -->

        <section class="obra-main-card">

            <div class="obra-main-top">

                <div class="obra-section-title">

                    <div class="obra-section-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>

                    <div>
                        <h2>Información general</h2>
                        <p>Datos principales del proyecto.</p>
                    </div>

                </div>

                <span class="obra-status <?= $estadoClase ?>">
                    <?= htmlspecialchars($estado) ?>
                </span>

            </div>


            <div class="obra-info-grid">

                <div class="obra-info-item">

                    <div class="obra-info-label">
                        <i class="fa-solid fa-user"></i>
                        Cliente
                    </div>

                    <strong class="obra-info-value">
                        <?= htmlspecialchars($nombreCliente ?: "Sin cliente") ?>
                    </strong>

                </div>


                <div class="obra-info-item">

                    <div class="obra-info-label">
                        <i class="fa-solid fa-user-tie"></i>
                        Jefe de obra
                    </div>

                    <strong class="obra-info-value <?= empty($nombreJefe) ? 'obra-info-empty' : '' ?>">

                        <?= htmlspecialchars($nombreJefe ?: "Sin asignar") ?>

                    </strong>

                </div>


                <div class="obra-info-item">

                    <div class="obra-info-label">
                        <i class="fa-solid fa-location-dot"></i>
                        Dirección
                    </div>

                    <strong class="obra-info-value">
                        <?= htmlspecialchars($detalle["direccion"] ?? "Sin dirección") ?>
                    </strong>

                </div>

            </div>


            <div class="obra-description">

                <div class="obra-info-label">
                    <i class="fa-solid fa-align-left"></i>
                    Descripción del proyecto
                </div>

                <p>
                    <?= !empty($detalle["descripcion"])
                        ? nl2br(htmlspecialchars($detalle["descripcion"]))
                        : "No se registró una descripción para esta obra." ?>
                </p>

            </div>

        </section>


        <!-- RESUMEN -->

        <div class="obra-section-header">

            <div>
                <h2>Resumen de la obra</h2>
                <p>Indicadores generales del progreso del proyecto.</p>
            </div>

            <div class="obra-section-line"></div>

        </div>


        <section class="obra-stats-grid">


            <div class="obra-stat-card">

                <div class="obra-stat-top">

                    <div class="obra-stat-icon">
                        <i class="fa-solid fa-list-check"></i>
                    </div>

                </div>

                <h3 class="obra-stat-number">
                    <?= (int) ($resumen["total"] ?? 0) ?>
                </h3>

                <p class="obra-stat-label">
                    Total de etapas
                </p>

            </div>


            <div class="obra-stat-card stat-finalizadas">

                <div class="obra-stat-top">

                    <div class="obra-stat-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                </div>

                <h3 class="obra-stat-number">
                    <?= (int) ($resumen["finalizadas"] ?? 0) ?>
                </h3>

                <p class="obra-stat-label">
                    Etapas finalizadas
                </p>

            </div>


            <div class="obra-stat-card stat-proceso">

                <div class="obra-stat-top">

                    <div class="obra-stat-icon">
                        <i class="fa-solid fa-person-digging"></i>
                    </div>

                </div>

                <h3 class="obra-stat-number">
                    <?= (int) ($resumen["proceso"] ?? 0) ?>
                </h3>

                <p class="obra-stat-label">
                    En proceso
                </p>

            </div>


            <div class="obra-stat-card stat-pendientes">

                <div class="obra-stat-top">

                    <div class="obra-stat-icon">
                        <i class="fa-regular fa-clock"></i>
                    </div>

                </div>

                <h3 class="obra-stat-number">
                    <?= (int) ($resumen["pendientes"] ?? 0) ?>
                </h3>

                <p class="obra-stat-label">
                    Etapas pendientes
                </p>

            </div>


            <div class="obra-stat-card stat-avance">

                <div class="obra-stat-top">

                    <div class="obra-stat-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                </div>

                <h3 class="obra-stat-number">
                    <?= number_format($avance, 1, ",", ".") ?>%
                </h3>

                <p class="obra-stat-label">
                    Avance general
                </p>

                <div
                    class="obra-progress"
                    role="progressbar"
                    aria-valuenow="<?= $avance ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="Avance general de la obra"
                >

                    <div
                        class="obra-progress-bar"
                        style="width: <?= $avance ?>%;">
                    </div>

                </div>

            </div>


        </section>


        <!-- MÓDULOS -->

        <div class="obra-section-header">

            <div>
                <h2>Módulos de la obra</h2>
                <p>Accedé a las herramientas de gestión y seguimiento.</p>
            </div>

            <div class="obra-section-line"></div>

        </div>


        <section class="obra-modules-grid">


            <!-- ETAPAS -->

            <a
                href="etapas/index.php?id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>

                <h3 class="obra-module-title">
                    Etapas
                </h3>

                <p class="obra-module-description">
                    Administrá las etapas y el desarrollo del proyecto.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- DIARIO DE OBRA -->

            <a
                href="../../controladores/AvanceController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>

                <h3 class="obra-module-title">
                    Diario de obra
                </h3>

                <p class="obra-module-description">
                    Consultá los avances y registros diarios del proyecto.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- EMPLEADOS -->

            <a
                href="../../controladores/EmpleadoObraController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <h3 class="obra-module-title">
                    Empleados
                </h3>

                <p class="obra-module-description">
                    Gestioná el personal asignado a esta obra.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- MATERIALES -->

            <a
                href="../../controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>

                <h3 class="obra-module-title">
                    Materiales
                </h3>

                <p class="obra-module-description">
                    Consultá y gestioná las solicitudes de materiales.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- HERRAMIENTAS -->

            <a
                href="herramientas/index.php?id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>

                <h3 class="obra-module-title">
                    Herramientas
                </h3>

                <p class="obra-module-description">
                    Administrá las herramientas utilizadas en la obra.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- INCIDENCIAS -->

            <a
                href="../../controladores/IncidenciaController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="obra-module-title">
                    Incidencias
                </h3>

                <p class="obra-module-description">
                    Registrá y consultá los problemas de la obra.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- FOTOS -->

            <a
                href="../../controladores/FotoObraController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-camera"></i>
                </div>

                <h3 class="obra-module-title">
                    Fotografías
                </h3>

                <p class="obra-module-description">
                    Visualizá y administrá las imágenes del proyecto.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


            <!-- DOCUMENTOS -->

            <a
                href="../../controladores/DocumentoObraController.php?accion=listar&id_obra=<?= $idObra ?>"
                class="obra-module-card"
            >

                <div class="obra-module-icon">
                    <i class="fa-solid fa-folder-open"></i>
                </div>

                <h3 class="obra-module-title">
                    Documentos
                </h3>

                <p class="obra-module-description">
                    Accedé a los documentos y archivos de la obra.
                </p>

                <div class="obra-module-action">
                    <span>Ingresar al módulo</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>

            </a>


        </section>

    </div>

</main>

<?php require_once "../../layouts/footer.php"; ?>
