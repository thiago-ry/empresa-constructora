<?php

/** @var array $herramienta */

require_once __DIR__ . "/../../config/permisos.php";
verificarPermiso("herramientas");

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                <?= strtoupper(htmlspecialchars($herramienta["nombre"])) ?>
            </h1>

            <p class="page-subtitle">
                Información y gestión de la herramienta.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="/empresa_constructora/controladores/HerramientaController.php?accion=editar&id=<?= (int) $herramienta["id_herramienta"] ?>"
                class="btn btn-warning">
                <i class="fa-solid fa-pen"></i>
                Editar
            </a>

            <a
                href="/empresa_constructora/vistas/herramientas/"
                class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>

        </div>

    </div>


    <!-- =====================================================
     RESUMEN
====================================================== -->

    <div class="retiro-resumen-grid">

        <div class="retiro-resumen-card">

            <div class="retiro-resumen-icon">
                <i class="fa-solid fa-toolbox"></i>
            </div>

            <div class="retiro-resumen-info">

                <span>Total de unidades</span>

                <strong>
                    <?= (int) $herramienta["cantidad_total"] ?>
                </strong>

            </div>

        </div>


        <div class="retiro-resumen-card">

            <div class="retiro-resumen-icon success">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div class="retiro-resumen-info">

                <span>Disponibles</span>

                <strong>
                    <?= (int) $herramienta["cantidad_disponible"] ?>
                </strong>

            </div>

        </div>


        <div class="retiro-resumen-card">

            <div class="retiro-resumen-icon warning">
                <i class="fa-solid fa-hard-hat"></i>
            </div>

            <div class="retiro-resumen-info">

                <span>Asignadas</span>

                <strong>
                    <?= (int) $herramienta["cantidad_asignada"] ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- =====================================================
     INFORMACIÓN GENERAL
====================================================== -->

    <div class="form-card herramienta-card">

        <div class="form-card-header">

            <div>

                <h2>
                    <i class="fa-solid fa-circle-info"></i>
                    Información general
                </h2>

                <p>
                    Datos registrados de esta herramienta.
                </p>

            </div>

        </div>


        <div class="herramienta-info-grid">

            <div class="herramienta-info-item">

                <span>Nombre</span>

                <strong>
                    <?= htmlspecialchars($herramienta["nombre"]) ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Tipo</span>

                <strong>
                    <?= htmlspecialchars($herramienta["tipo"]) ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Marca</span>

                <strong>
                    <?= htmlspecialchars($herramienta["marca"] ?: "-") ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Modelo</span>

                <strong>
                    <?= htmlspecialchars($herramienta["modelo"] ?: "-") ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Cantidad registrada</span>

                <strong>
                    <?= (int) $herramienta["cantidad_total"] ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Costo</span>

                <strong>
                    $
                    <?= number_format(
                        $herramienta["costo"],
                        2,
                        ",",
                        "."
                    ) ?>
                </strong>

            </div>


            <div class="herramienta-info-item">

                <span>Fecha de adquisición</span>

                <strong>

                    <?= !empty($herramienta["fecha_adquisicion"])
                        ? date(
                            "d/m/Y",
                            strtotime($herramienta["fecha_adquisicion"])
                        )
                        : "-"
                    ?>

                </strong>

            </div>

        </div>

    </div>


    <!-- =====================================================
     MÓDULOS RELACIONADOS
====================================================== -->

    <div class="form-card herramienta-card">

        <div class="form-card-header">

            <div>

                <h2>
                    <i class="fa-solid fa-layer-group"></i>
                    Módulos relacionados
                </h2>

                <p>
                    Acceda a la información y gestión relacionada
                    con esta herramienta.
                </p>

            </div>

        </div>


        <div class="herramienta-modulos-grid">


            <!-- =================================================
             UNIDADES
        ================================================== -->

            <div class="herramienta-module">

                <div class="herramienta-module-icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>

                <div class="herramienta-module-content">

                    <h3>
                        Unidades
                    </h3>

                    <p>
                        Consulte todas las unidades individuales
                        registradas para esta herramienta.
                    </p>

                    <button
                        type="button"
                        id="btnVerUnidades"
                        class="btn btn-primary">
                        <i class="fa-solid fa-eye"></i>
                        Ver unidades
                    </button>

                </div>

            </div>


            <!-- =================================================
             HISTORIAL
        ================================================== -->

            <div class="herramienta-module">

                <div class="herramienta-module-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div class="herramienta-module-content">

                    <h3>
                        Historial de asignaciones
                    </h3>

                    <p>
                        Consulte las obras donde esta herramienta
                        fue utilizada o asignada.
                    </p>

                    <a
                        href="#"
                        class="btn btn-primary">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Ver historial
                    </a>

                </div>

            </div>


            <!-- =================================================
             MANTENIMIENTOS
        ================================================== -->

            <div class="herramienta-module">

                <div class="herramienta-module-icon">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>

                <div class="herramienta-module-content">

                    <h3>
                        Mantenimientos
                    </h3>

                    <p>
                        Consulte las reparaciones y mantenimientos
                        realizados sobre esta herramienta.
                    </p>

                    <a
                        href="#"
                        class="btn btn-primary">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        Ver mantenimientos
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<!-- =========================================================
     MODAL UNIDADES
========================================================= -->

<div
    id="modalUnidades"
    class="modal-unidades-overlay">

    <div
        class="modal-unidades"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalNombreHerramienta">


        <!-- =================================================
         CABECERA
    ================================================== -->

        <div class="modal-unidades-header">

            <div>

                <div class="modal-section-label">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    Gestión de unidades
                </div>

                <h2 id="modalNombreHerramienta">
                    Unidades
                </h2>

                <p id="modalDescripcionHerramienta">
                    Unidades individuales de la herramienta.
                </p>

            </div>


            <button
                type="button"
                class="modal-unidades-close"
                id="btnCerrarUnidades"
                aria-label="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        <!-- =================================================
         RESUMEN
    ================================================== -->

        <div class="modal-unidades-resumen">

            <div class="unidad-resumen-item">

                <span>Total</span>

                <strong id="modalTotal">
                    0
                </strong>

            </div>


            <div class="unidad-resumen-item disponible">

                <span>Disponibles</span>

                <strong id="modalDisponibles">
                    0
                </strong>

            </div>


            <div class="unidad-resumen-item asignada">

                <span>Asignadas</span>

                <strong id="modalAsignadas">
                    0
                </strong>

            </div>


            <div class="unidad-resumen-item reparacion">

                <span>En reparación</span>

                <strong id="modalReparacion">
                    0
                </strong>

            </div>


            <div class="unidad-resumen-item fuera-servicio">

                <span>Fuera de servicio</span>

                <strong id="modalFueraServicio">
                    0
                </strong>

            </div>

        </div>


        <!-- =================================================
         CONTENIDO
    ================================================== -->

        <div class="modal-unidades-body">


            <!-- CARGANDO -->

            <div
                id="unidadesLoading"
                class="unidades-loading">

                <i class="fa-solid fa-spinner fa-spin"></i>

                <span>
                    Cargando unidades...
                </span>

            </div>


            <!-- ERROR -->

            <div
                id="unidadesError"
                class="unidades-error"
                style="display:none;">

                <div class="unidades-error-icon">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>

                <div>

                    <strong>
                        No se pudieron cargar las unidades
                    </strong>

                    <p id="mensajeError">
                        Ocurrió un error al obtener la información.
                    </p>

                </div>

            </div>


            <!-- TABLA -->

            <div
                id="tablaUnidadesContainer"
                class="tabla-unidades-container"
                style="display:none;">

                <table class="tabla-unidades">

                    <thead>

                        <tr>

                            <th>
                                Unidad
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody id="tablaUnidadesBody">
                    </tbody>

                </table>

            </div>


            <!-- SIN UNIDADES -->

            <div
                id="sinUnidades"
                class="sin-unidades"
                style="display:none;">

                <div class="sin-unidades-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>

                <strong>
                    No hay unidades registradas
                </strong>

                <p>
                    Esta herramienta no tiene unidades individuales
                    registradas actualmente.
                </p>

            </div>

        </div>


        <!-- =================================================
         FOOTER
    ================================================== -->

        <div class="modal-unidades-footer">

            <button
                type="button"
                class="btn btn-secondary"
                id="btnCerrarUnidadesFooter">
                <i class="fa-solid fa-xmark"></i>
                Cerrar
            </button>

        </div>

    </div>

</div>

<!-- =========================================================
     ESTILOS
========================================================= -->

<style>
    /* =========================================================
   RESUMEN
========================================================= */

    .retiro-resumen-grid {

        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 18px;

        margin-bottom: 20px;
    }


    .retiro-resumen-card {

        display: flex;

        align-items: center;

        gap: 16px;

        padding: 20px;

        background: var(--card-bg, #0b1320);

        border: 1px solid rgba(128, 128, 128, 0.18);

        border-radius: 12px;

        box-shadow:
            0 4px 12px rgba(0, 0, 0, 0.06);

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }


    .retiro-resumen-card:hover {

        transform: translateY(-2px);

        box-shadow:
            0 8px 20px rgba(0, 0, 0, 0.10);
    }


    .retiro-resumen-icon {

        width: 48px;

        height: 48px;

        min-width: 48px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        background:
            rgba(59, 130, 246, 0.12);

        color:
            #2563eb;

        font-size: 20px;
    }


    .retiro-resumen-icon.success {

        background:
            rgba(34, 197, 94, 0.12);

        color:
            #16a34a;
    }


    .retiro-resumen-icon.warning {

        background:
            rgba(245, 158, 11, 0.14);

        color:
            #d97706;
    }


    .retiro-resumen-info span {

        display: block;

        margin-bottom: 5px;

        font-size: 13px;

        opacity: 0.68;
    }


    .retiro-resumen-info strong {

        display: block;

        font-size: 26px;

        line-height: 1.1;
    }


    /* =========================================================
   CARD PRINCIPAL
========================================================= */

    .herramienta-card {

        margin-top: 20px;

        overflow: hidden;
    }


    .form-card-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding-bottom: 20px;

        margin-bottom: 24px;

        border-bottom:
            1px solid rgba(128, 128, 128, 0.18);
    }


    .form-card-header h2 {

        display: flex;

        align-items: center;

        gap: 9px;

        margin: 0;

        font-size: 20px;
    }


    .form-card-header h2 i {

        font-size: 17px;

        opacity: 0.72;
    }


    .form-card-header p {

        margin: 6px 0 0;

        font-size: 13px;

        opacity: 0.65;
    }


    /* =========================================================
   INFORMACIÓN GENERAL
========================================================= */

    .herramienta-info-grid {

        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        column-gap: 40px;

        row-gap: 0;
    }


    .herramienta-info-item {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        min-height: 62px;

        padding: 14px 0;

        border-bottom:
            1px solid rgba(128, 128, 128, 0.14);
    }


    .herramienta-info-item span {

        font-size: 13px;

        opacity: 0.62;
    }


    .herramienta-info-item strong {

        font-size: 14px;

        text-align: right;

        font-weight: 600;
    }


    /* =========================================================
   MÓDULOS
========================================================= */

    .herramienta-modulos-grid {

        display: grid;

        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 18px;
    }


    .herramienta-module {

        display: flex;

        flex-direction: column;

        padding: 20px;

        border:
            1px solid rgba(128, 128, 128, 0.18);

        border-radius: 11px;

        background:
            rgba(128, 128, 128, 0.035);

        transition:
            border-color 0.2s ease,
            transform 0.2s ease;
    }


    .herramienta-module:hover {

        border-color:
            rgba(59, 130, 246, 0.35);

        transform: translateY(-2px);
    }


    .herramienta-module-icon {

        width: 44px;

        height: 44px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 16px;

        border-radius: 9px;

        background:
            rgba(59, 130, 246, 0.10);

        color:
            #2563eb;

        font-size: 18px;
    }


    .herramienta-module-content {

        display: flex;

        flex-direction: column;

        flex: 1;
    }


    .herramienta-module h3 {

        margin: 0 0 8px;

        font-size: 16px;
    }


    .herramienta-module p {

        margin: 0 0 18px;

        min-height: 58px;

        font-size: 13px;

        line-height: 1.55;

        opacity: 0.65;
    }


    .herramienta-module .btn {

        align-self: flex-start;

        margin-top: auto;
    }


    /* =========================================================
   MODAL
========================================================= */

    .modal-unidades-overlay {

        display: none;

        position: fixed;

        inset: 0;

        z-index: 9999;

        background:
            rgba(0, 0, 0, 0.68);

        align-items: center;

        justify-content: center;

        padding: 20px;

        backdrop-filter: blur(5px);
    }


    .modal-unidades-overlay.active {

        display: flex;
    }


    .modal-unidades {

        width: 100%;

        max-width: 880px;

        max-height: 90vh;

        display: flex;

        flex-direction: column;

        background:
            var(--card-bg, #0b1320);

        border:
            1px solid rgba(128, 128, 128, 0.18);

        border-radius: 13px;

        overflow: hidden;

        box-shadow:
            0 25px 70px rgba(0, 0, 0, 0.30);

        animation:
            modalEntrada 0.2s ease;
    }


    @keyframes modalEntrada {

        from {

            opacity: 0;

            transform:
                translateY(-12px) scale(0.985);

        }

        to {

            opacity: 1;

            transform:
                translateY(0) scale(1);

        }

    }


    /* =========================================================
   HEADER MODAL
========================================================= */

    .modal-unidades-header {

        display: flex;

        align-items: flex-start;

        justify-content: space-between;

        gap: 20px;

        padding: 24px 28px;

        border-bottom:
            1px solid rgba(128, 128, 128, 0.18);
    }


    .modal-section-label {

        display: flex;

        align-items: center;

        gap: 7px;

        margin-bottom: 7px;

        font-size: 11px;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.7px;

        opacity: 0.58;
    }


    .modal-unidades-header h2 {

        margin: 0;

        font-size: 23px;
    }


    .modal-unidades-header p {

        margin: 5px 0 0;

        font-size: 13px;

        opacity: 0.62;
    }


    .modal-unidades-close {

        width: 38px;

        height: 38px;

        display: flex;

        align-items: center;

        justify-content: center;

        flex-shrink: 0;

        border:
            1px solid rgba(128, 128, 128, 0.18);

        border-radius: 8px;

        background: transparent;

        color: inherit;

        cursor: pointer;

        font-size: 17px;

        transition:
            background 0.2s ease,
            border-color 0.2s ease;
    }


    .modal-unidades-close:hover {

        background:
            rgba(128, 128, 128, 0.10);

        border-color:
            rgba(128, 128, 128, 0.30);
    }


    /* =========================================================
   RESUMEN DEL MODAL
========================================================= */

    .modal-unidades-resumen {

        display: grid;

        grid-template-columns:
            repeat(3, 1fr);

        gap: 10px;

        padding: 18px 28px;

        background:
            rgba(128, 128, 128, 0.035);

        border-bottom:
            1px solid rgba(128, 128, 128, 0.12);
    }


    .unidad-resumen-item {

        padding: 13px 15px;

        border:
            1px solid rgba(128, 128, 128, 0.15);

        border-radius: 8px;

        background:
            rgba(128, 128, 128, 0.04);
    }


    .unidad-resumen-item span {

        display: block;

        margin-bottom: 5px;

        font-size: 11px;

        text-transform: uppercase;

        letter-spacing: 0.4px;

        opacity: 0.62;
    }


    .unidad-resumen-item strong {

        display: block;

        font-size: 21px;
    }


    .unidad-resumen-item.disponible {

        border-left:
            3px solid #22c55e;
    }


    .unidad-resumen-item.asignada {

        border-left:
            3px solid #3b82f6;
    }


    .unidad-resumen-item.reparacion {

        border-left:
            3px solid #f59e0b;
    }


    .unidad-resumen-item.fuera-servicio {

        border-left:
            3px solid #ef4444;
    }



    /* =========================================================
   BODY
========================================================= */

    .modal-unidades-body {

        padding: 24px 28px;

        min-height: 180px;

        max-height: 430px;

        overflow-y: auto;
    }


    .unidades-loading {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 10px;

        min-height: 150px;

        font-size: 14px;

        opacity: 0.62;
    }


    .unidades-loading i {

        font-size: 17px;
    }


    /* =========================================================
   ERROR
========================================================= */

    .unidades-error {

        display: flex;

        align-items: center;

        gap: 14px;

        padding: 18px;

        border:
            1px solid rgba(239, 68, 68, 0.25);

        border-left:
            4px solid #ef4444;

        border-radius: 8px;

        background:
            rgba(239, 68, 68, 0.07);
    }


    .unidades-error-icon {

        width: 38px;

        height: 38px;

        display: flex;

        align-items: center;

        justify-content: center;

        flex-shrink: 0;

        border-radius: 8px;

        background:
            rgba(239, 68, 68, 0.12);

        color:
            #dc2626;
    }


    .unidades-error strong {

        display: block;

        margin-bottom: 3px;

        font-size: 14px;
    }


    .unidades-error p {

        margin: 0;

        font-size: 13px;

        opacity: 0.65;
    }


    /* =========================================================
   TABLA
========================================================= */

    .tabla-unidades-container {

        width: 100%;

        overflow-x: auto;
    }


    .tabla-unidades {

        width: 100%;

        border-collapse: collapse;
    }


    .tabla-unidades th,

    .tabla-unidades td {

        padding:
            13px 15px;

        text-align: left;

        border-bottom:
            1px solid rgba(128, 128, 128, 0.14);
    }


    .tabla-unidades th {

        font-size: 11px;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        opacity: 0.60;
    }


    .tabla-unidades td {

        font-size: 13px;
    }


    .tabla-unidades tbody tr {

        transition:
            background 0.15s ease;
    }


    .tabla-unidades tbody tr:hover {

        background:
            rgba(128, 128, 128, 0.045);
    }


    /* =========================================================
   ESTADOS
========================================================= */

    .estado-unidad {

        display: inline-flex;

        align-items: center;

        padding:
            5px 10px;

        border-radius: 6px;

        font-size: 11px;

        font-weight: 600;

        border: 1px solid transparent;
    }


    .estado-disponible {

        background:
            rgba(34, 197, 94, 0.10);

        color:
            #16a34a;

        border-color:
            rgba(34, 197, 94, 0.20);
    }


    .estado-asignada {

        background:
            rgba(59, 130, 246, 0.10);

        color:
            #2563eb;

        border-color:
            rgba(59, 130, 246, 0.20);
    }


    .estado-reparacion {

        background:
            rgba(245, 158, 11, 0.10);

        color:
            #d97706;

        border-color:
            rgba(245, 158, 11, 0.20);
    }


    .estado-fuera {

        background:
            rgba(239, 68, 68, 0.10);

        color:
            #dc2626;

        border-color:
            rgba(239, 68, 68, 0.20);
    }




    .estado-otro {

        background:
            rgba(128, 128, 128, 0.10);

        color: inherit;

        border-color:
            rgba(128, 128, 128, 0.18);
    }


    /* =========================================================
   SIN UNIDADES
========================================================= */

    .sin-unidades {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        min-height: 190px;

        text-align: center;
    }


    .sin-unidades-icon {

        width: 52px;

        height: 52px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 12px;

        border-radius: 10px;

        background:
            rgba(128, 128, 128, 0.08);

        font-size: 21px;

        opacity: 0.60;
    }


    .sin-unidades strong {

        margin-bottom: 5px;

        font-size: 15px;
    }


    .sin-unidades p {

        max-width: 420px;

        margin: 0;

        font-size: 13px;

        opacity: 0.60;
    }


    /* =========================================================
   FOOTER
========================================================= */

    .modal-unidades-footer {

        display: flex;

        justify-content: flex-end;

        padding:
            16px 28px;

        border-top:
            1px solid rgba(128, 128, 128, 0.18);
    }


    /* =========================================================
   RESPONSIVE
========================================================= */

    @media (max-width: 900px) {

        .herramienta-modulos-grid {

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

        }

    }


    @media (max-width: 750px) {

        .retiro-resumen-grid {

            grid-template-columns:
                1fr;

        }


        .herramienta-info-grid {

            grid-template-columns:
                1fr;

        }


        .herramienta-modulos-grid {

            grid-template-columns:
                1fr;

        }


        .modal-unidades-resumen {

            grid-template-columns:
                repeat(2, 1fr);

        }

    }


    @media (max-width: 550px) {

        .page-header {

            flex-direction: column;

            align-items: flex-start;

            gap: 15px;

        }


        .page-header-actions {

            width: 100%;

            display: flex;

        }


        .page-header-actions .btn {

            flex: 1;

        }


        .herramienta-info-item {

            align-items: flex-start;

            flex-direction: column;

            gap: 5px;

        }


        .herramienta-info-item strong {

            text-align: left;

        }


        .modal-unidades {

            max-height: 95vh;

        }


        .modal-unidades-header,

        .modal-unidades-body,

        .modal-unidades-footer {

            padding-left: 18px;

            padding-right: 18px;

        }


        .modal-unidades-resumen {

            padding-left: 18px;

            padding-right: 18px;

        }

    }
</style>

<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {


            const modal =
                document.getElementById(
                    "modalUnidades"
                );


            const btnAbrir =
                document.getElementById(
                    "btnVerUnidades"
                );


            const btnCerrar =
                document.getElementById(
                    "btnCerrarUnidades"
                );


            const btnCerrarFooter =
                document.getElementById(
                    "btnCerrarUnidadesFooter"
                );


            const loading =
                document.getElementById(
                    "unidadesLoading"
                );


            const error =
                document.getElementById(
                    "unidadesError"
                );


            const mensajeError =
                document.getElementById(
                    "mensajeError"
                );


            const tablaContainer =
                document.getElementById(
                    "tablaUnidadesContainer"
                );


            const tablaBody =
                document.getElementById(
                    "tablaUnidadesBody"
                );


            const sinUnidades =
                document.getElementById(
                    "sinUnidades"
                );


            const idHerramienta =
                <?= (int) $herramienta["id_herramienta"] ?>;


            /* =====================================================
               ABRIR MODAL
            ====================================================== */

            btnAbrir.addEventListener(
                "click",
                function() {

                    modal.classList.add(
                        "active"
                    );

                    document.body.style.overflow =
                        "hidden";

                    cargarUnidades();

                }
            );


            /* =====================================================
               CERRAR MODAL
            ====================================================== */

            function cerrarModal() {

                modal.classList.remove(
                    "active"
                );

                document.body.style.overflow =
                    "";

            }


            btnCerrar.addEventListener(
                "click",
                cerrarModal
            );


            btnCerrarFooter.addEventListener(
                "click",
                cerrarModal
            );


            /* =====================================================
               CLICK FUERA
            ====================================================== */

            modal.addEventListener(
                "click",
                function(event) {

                    if (
                        event.target === modal
                    ) {

                        cerrarModal();

                    }

                }
            );


            /* =====================================================
               ESC
            ====================================================== */

            document.addEventListener(
                "keydown",
                function(event) {

                    if (
                        event.key === "Escape" &&
                        modal.classList.contains(
                            "active"
                        )
                    ) {

                        cerrarModal();

                    }

                }
            );


            /* =====================================================
               CARGAR UNIDADES
            ====================================================== */

            function cargarUnidades() {


                loading.style.display =
                    "flex";

                error.style.display =
                    "none";

                tablaContainer.style.display =
                    "none";

                sinUnidades.style.display =
                    "none";


                tablaBody.innerHTML =
                    "";


                const url =
                    "/empresa_constructora/controladores/HerramientaController.php" +
                    "?accion=unidades&id=" +
                    idHerramienta;


                fetch(url)

                    .then(
                        function(response) {

                            if (!response.ok) {

                                throw new Error(
                                    "Error HTTP " +
                                    response.status
                                );

                            }

                            return response.json();

                        }
                    )

                    .then(
                        function(data) {


                            loading.style.display =
                                "none";


                            if (!data.success) {

                                throw new Error(
                                    data.message ||
                                    "No se pudieron obtener las unidades."
                                );

                            }


                            /* =================================
                               HERRAMIENTA
                            ================================= */

                            const herramienta =
                                data.herramienta;


                            document.getElementById(
                                    "modalNombreHerramienta"
                                ).textContent =
                                herramienta.nombre;


                            let descripcion = "";


                            if (herramienta.marca) {

                                descripcion +=
                                    herramienta.marca;

                            }


                            if (herramienta.modelo) {

                                if (
                                    descripcion !== ""
                                ) {

                                    descripcion +=
                                        " · ";

                                }

                                descripcion +=
                                    herramienta.modelo;

                            }


                            if (
                                descripcion === ""
                            ) {

                                descripcion =
                                    "Unidades individuales de la herramienta.";

                            }


                            document.getElementById(
                                    "modalDescripcionHerramienta"
                                ).textContent =
                                descripcion;


                            /* =================================
                               RESUMEN
                            ================================= */

                            document.getElementById(
                                    "modalTotal"
                                ).textContent =
                                data.resumen.total;


                            document.getElementById(
                                    "modalDisponibles"
                                ).textContent =
                                data.resumen.disponibles;


                            document.getElementById(
                                    "modalAsignadas"
                                ).textContent =
                                data.resumen.asignadas;


                            document.getElementById(
                                    "modalReparacion"
                                ).textContent =
                                data.resumen.reparacion;


                            document.getElementById(
                                    "modalFueraServicio"
                                ).textContent =
                                data.resumen.fuera_servicio;


                            /* =================================
                               SIN UNIDADES
                            ================================= */

                            if (
                                !data.unidades ||
                                data.unidades.length === 0
                            ) {

                                sinUnidades.style.display =
                                    "flex";

                                return;

                            }


                            /* =================================
                               TABLA
                            ================================= */

                            data.unidades.forEach(
                                function(unidad) {


                                    const tr =
                                        document.createElement(
                                            "tr"
                                        );


                                    const tdNumero =
                                        document.createElement(
                                            "td"
                                        );


                                    tdNumero.innerHTML =
                                        "<strong>Unidad #" +
                                        escapeHtml(
                                            unidad.numero_unidad
                                        ) +
                                        "</strong>";


                                    const tdEstado =
                                        document.createElement(
                                            "td"
                                        );


                                    const estado =
                                        unidad.estado ||
                                        "Sin estado";


                                    const claseEstado =
                                        obtenerClaseEstado(
                                            estado
                                        );


                                    tdEstado.innerHTML =
                                        '<span class="estado-unidad ' +
                                        claseEstado +
                                        '">' +
                                        escapeHtml(
                                            estado
                                        ) +
                                        "</span>";


                                    tr.appendChild(
                                        tdNumero
                                    );

                                    tr.appendChild(
                                        tdEstado
                                    );

                                    tablaBody.appendChild(
                                        tr
                                    );

                                }
                            );


                            tablaContainer.style.display =
                                "block";

                        }
                    )

                    .catch(
                        function(err) {


                            console.error(
                                err
                            );


                            loading.style.display =
                                "none";


                            error.style.display =
                                "flex";


                            mensajeError.textContent =
                                err.message ||
                                "No se pudieron cargar las unidades.";

                        }
                    );

            }


            /* =====================================================
               ESTADO VISUAL
            ====================================================== */

            function obtenerClaseEstado(
                estado
            ) {


                const normalizado =
                    estado
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(
                        /[\u0300-\u036f]/g,
                        ""
                    );


                switch (normalizado) {

                    case "disponible":

                        return "estado-disponible";


                    case "asignada":

                        return "estado-asignada";


                    case "en reparacion":

                        return "estado-reparacion";


                    case "fuera de servicio":

                        return "estado-fuera";

                    default:

                        return "estado-otro";

                }

            }


            /* =====================================================
               ESCAPAR HTML
            ====================================================== */

            function escapeHtml(
                valor
            ) {

                return String(valor)

                    .replace(
                        /&/g,
                        "&amp;"
                    )

                    .replace(
                        /</g,
                        "&lt;"
                    )

                    .replace(
                        />/g,
                        "&gt;"
                    )

                    .replace(
                        /"/g,
                        "&quot;"
                    )

                    .replace(
                        /'/g,
                        "&#039;"
                    );

            }

        }
    );
</script>

<?php

require_once __DIR__ . "/../../layouts/footer.php";

?>