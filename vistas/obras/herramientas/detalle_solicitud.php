
<?php

/**
 * @var array $solicitud
 * @var array $detalles
 */

$titulo = "Detalle de solicitud";

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitud = $solicitud ?? [];
$detalles = $detalles ?? [];

$idSolicitud = (int) ($solicitud["id_solicitud"] ?? 0);
$estado = $solicitud["estado"] ?? "Pendiente";

$clasesEstado = [
    "Pendiente" => "estado-pendiente",
    "Aprobada" => "estado-aprobada",
    "Rechazada" => "estado-rechazada",
    "Entregada" => "estado-entregada",
    "Parcialmente devuelta" => "estado-parcial",
    "Devuelta" => "estado-devuelta"
];

$claseEstado = $clasesEstado[$estado] ?? "estado-pendiente";
?>

<main class="content fade-up">

    <div class="page-header">
        <div>
            <span class="badge-id">GESTIÓN DE HERRAMIENTAS</span>
            <h1 style="margin-top: 12px;">
                Solicitud #<?= $idSolicitud ?>
            </h1>
            <p>
                Consultá la información y el detalle de las herramientas solicitadas.
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
            class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver a solicitudes
        </a>
    </div>

    <div class="detalle-grid">

        <section class="form-card">
            <div class="card-header-clean">
                <div class="title-with-icon">
                    <i class="fa-solid fa-clipboard-list text-primary"></i>
                    <div>
                        <h2>Información de la solicitud</h2>
                        <p style="margin-top: 5px;">
                            Datos generales de la solicitud registrada.
                        </p>
                    </div>
                </div>
            </div>

            <div class="datos-solicitud">

                <div class="dato-item">
                    <span class="dato-etiqueta">
                        <i class="fa-solid fa-hashtag"></i>
                        Número de solicitud
                    </span>
                    <strong>#<?= $idSolicitud ?></strong>
                </div>

                <div class="dato-item">
                    <span class="dato-etiqueta">
                        <i class="fa-solid fa-helmet-safety"></i>
                        Obra
                    </span>
                    <strong>
                        <?= htmlspecialchars(
                            $solicitud["obra"] ?? $solicitud["nombre_obra"] ?? "No especificada",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>
                </div>

                <div class="dato-item">
                    <span class="dato-etiqueta">
                        <i class="fa-solid fa-calendar-days"></i>
                        Fecha de solicitud
                    </span>
                    <strong>
                        <?= htmlspecialchars(
                            $solicitud["fecha"] ?? "",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>
                </div>

                <div class="dato-item">
                    <span class="dato-etiqueta">
                        <i class="fa-solid fa-circle-info"></i>
                        Estado actual
                    </span>
                    <span class="estado-solicitud <?= $claseEstado ?>">
                        <span class="estado-punto"></span>
                        <?= htmlspecialchars($estado, ENT_QUOTES, "UTF-8") ?>
                    </span>
                </div>

            </div>

            <div class="observaciones-box">
                <h3>
                    <i class="fa-solid fa-comment-dots"></i>
                    Observaciones
                </h3>

                <?php if (!empty($solicitud["observaciones"])): ?>
                    <p><?= nl2br(htmlspecialchars(
                        $solicitud["observaciones"],
                        ENT_QUOTES,
                        "UTF-8"
                    )) ?></p>
                <?php else: ?>
                    <p class="text-secondary">
                        No se registraron observaciones para esta solicitud.
                    </p>
                <?php endif; ?>
            </div>
        </section>

        <section class="form-card">

            <div class="card-header-clean">
                <div class="title-with-icon">
                    <i class="fa-solid fa-screwdriver-wrench text-primary"></i>
                    <div>
                        <h2>Herramientas solicitadas</h2>
                        <p style="margin-top: 5px;">
                            Detalle de las herramientas y sus cantidades.
                        </p>
                    </div>
                </div>
            </div>

            <?php if (empty($detalles)): ?>

                <div class="empty-state">
                    <i class="fa-solid fa-toolbox"></i>
                    <h3>Sin herramientas registradas</h3>
                    <p>
                        Esta solicitud no tiene herramientas en su detalle.
                    </p>
                </div>

            <?php else: ?>

                <?php
                $totalUnidades = 0;

                foreach ($detalles as $d) {
                    $totalUnidades += (int) ($d["cantidad"] ?? 0);
                }
                ?>

                <div class="resumen-herramientas">
                    <div>
                        <span class="resumen-numero"><?= count($detalles) ?></span>
                        <span class="resumen-texto">
                            <?= count($detalles) === 1 ? "tipo de herramienta" : "tipos de herramientas" ?>
                        </span>
                    </div>

                    <div>
                        <span class="resumen-numero"><?= $totalUnidades ?></span>
                        <span class="resumen-texto">
                            <?= $totalUnidades === 1 ? "unidad solicitada" : "unidades solicitadas" ?>
                        </span>
                    </div>
                </div>

                <div class="tabla-responsive">
                    <table class="tabla-detalle">
                        <thead>
                            <tr>
                                <th>Herramienta</th>
                                <th>Cantidad solicitada</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($detalles as $d): ?>
                                <tr>
                                    <td>
                                        <div class="nombre-herramienta">
                                            <i class="fa-solid fa-toolbox"></i>
                                            <span>
                                                <?= htmlspecialchars(
                                                    $d["herramienta"] ?? $d["nombre_herramienta"] ?? "Sin nombre",
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="cantidad-badge">
                                            <?= (int) ($d["cantidad"] ?? 0) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

    </div>

</main>

<style>
    .detalle-grid {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .detalle-grid .form-card {
        min-width: 0;
    }

    .detalle-grid .card-header-clean {
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border);
    }

    .detalle-grid .card-header-clean h2 {
        color: var(--white);
        font-size: 19px;
        margin: 0;
    }

    .detalle-grid .card-header-clean p {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .datos-solicitud {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .dato-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 13px;
        min-width: 0;
    }

    .dato-etiqueta {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 12px;
    }

    .dato-etiqueta i {
        color: var(--primary);
    }

    .dato-item > strong {
        color: var(--text);
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .estado-solicitud {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .estado-punto {
        width: 7px;
        height: 7px;
        flex-shrink: 0;
        border-radius: 50%;
        background: currentColor;
    }

    .estado-pendiente {
        color: #f4c95d;
        background: rgba(244, 180, 0, 0.10);
        border-color: rgba(244, 180, 0, 0.20);
    }

    .estado-aprobada {
        color: #69b7ff;
        background: rgba(54, 144, 230, 0.10);
        border-color: rgba(54, 144, 230, 0.20);
    }

    .estado-rechazada {
        color: #ff8585;
        background: rgba(255, 90, 90, 0.10);
        border-color: rgba(255, 90, 90, 0.20);
    }

    .estado-entregada {
        color: #75dba0;
        background: rgba(46, 204, 113, 0.10);
        border-color: rgba(46, 204, 113, 0.20);
    }

    .estado-parcial {
        color: #c2a0ff;
        background: rgba(155, 110, 255, 0.10);
        border-color: rgba(155, 110, 255, 0.20);
    }

    .estado-devuelta {
        color: #91d8d0;
        background: rgba(58, 190, 175, 0.10);
        border-color: rgba(58, 190, 175, 0.20);
    }

    .observaciones-box {
        margin-top: 18px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 13px;
    }

    .observaciones-box h3 {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 12px;
        color: var(--text);
        font-size: 14px;
    }

    .observaciones-box h3 i {
        color: var(--primary);
    }

    .observaciones-box p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .resumen-herramientas {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .resumen-herramientas > div {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 140px;
        padding: 15px;
        background: rgba(244, 180, 0, 0.06);
        border: 1px solid rgba(244, 180, 0, 0.18);
        border-radius: 12px;
    }

    .resumen-numero {
        color: var(--primary);
        font-size: 24px;
        font-weight: 700;
    }

    .resumen-texto {
        color: var(--text-secondary);
        font-size: 12px;
    }

    .tabla-responsive {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: 13px;
    }

    .tabla-detalle {
        width: 100%;
        min-width: 380px;
        border-collapse: collapse;
    }

    .tabla-detalle thead {
        background: rgba(255, 255, 255, 0.03);
    }

    .tabla-detalle th {
        padding: 15px 18px;
        color: var(--text-secondary);
        text-align: left;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
    }

    .tabla-detalle td {
        padding: 16px 18px;
        color: var(--text);
        font-size: 13px;
        border-bottom: 1px solid var(--border);
    }

    .tabla-detalle tbody tr:last-child td {
        border-bottom: none;
    }

    .tabla-detalle tbody tr:hover {
        background: rgba(255, 255, 255, 0.025);
    }

    .nombre-herramienta {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nombre-herramienta > i {
        color: var(--primary);
    }

    .cantidad-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        padding: 7px 11px;
        background: rgba(244, 180, 0, 0.10);
        border: 1px solid rgba(244, 180, 0, 0.20);
        border-radius: 9px;
        color: var(--primary);
        font-weight: 700;
    }

    .detalle-acciones {
        display: flex;
        justify-content: flex-end;
        margin-top: 22px;
    }

    @media (max-width: 600px) {
        .datos-solicitud {
            grid-template-columns: 1fr;
        }

        .detalle-acciones {
            justify-content: stretch;
        }

        .detalle-acciones .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>