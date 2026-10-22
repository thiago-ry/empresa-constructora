
<?php

$titulo = "Mis solicitudes de herramientas";

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = $solicitudes ?? [];
?>

<main class="content fade-up">

    <div class="page-header">
        <div>
            <span class="badge-id">GESTIÓN DE HERRAMIENTAS</span>
            <h1 style="margin-top: 12px;">Mis solicitudes</h1>
            <p>
                Consultá el estado de tus solicitudes y revisá las herramientas que pediste para tu obra.
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=crear"
            class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            Nueva solicitud
        </a>
    </div>

    <?php if (!empty($_SESSION["success"])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <h3>
                <i class="fa-solid fa-circle-check"></i>
                Operación realizada
            </h3>
            <p>
                <?= htmlspecialchars($_SESSION["success"], ENT_QUOTES, "UTF-8") ?>
            </p>
        </div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION["error"])): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <h3>
                <i class="fa-solid fa-circle-exclamation"></i>
                No se pudo completar la operación
            </h3>
            <p>
                <?= htmlspecialchars($_SESSION["error"], ENT_QUOTES, "UTF-8") ?>
            </p>
        </div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <div class="form-card">

        <div class="card-header-clean" style="padding: 0 0 20px; margin-bottom: 20px;">
            <div class="title-with-icon">
                <i class="fa-solid fa-clipboard-list text-primary"></i>
                <div>
                    <h2>Historial de solicitudes</h2>
                    <p style="margin-top: 5px;">
                        <?= count($solicitudes) ?>
                        <?= count($solicitudes) === 1 ? "solicitud registrada" : "solicitudes registradas" ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if (empty($solicitudes)): ?>

            <div class="empty-state">
                <i class="fa-solid fa-toolbox"></i>
                <h3>Todavía no tenés solicitudes</h3>
                <p>
                    Cuando necesites herramientas para tu obra, podés crear una nueva solicitud desde acá.
                </p>

                <a
                    href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=crear"
                    class="btn btn-primary"
                    style="margin-top: 18px;">
                    <i class="fa-solid fa-plus"></i>
                    Crear solicitud
                </a>
            </div>

        <?php else: ?>

            <div class="tabla-responsive">
                <table class="tabla-solicitudes">
                    <thead>
                        <tr>
                            <th>N.º</th>
                            <th>Fecha</th>
                            <th>Solicitante</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <?php
                            $estado = $s["estado"] ?? "Pendiente";

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

                            <tr>
                                <td>
                                    <span class="badge-id">
                                        #<?= (int) $s["id_solicitud"] ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $s["fecha"] ?? "",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $s["solicitante"] ?? "Sin especificar",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                </td>

                                <td>
                                    <span class="estado-solicitud <?= $claseEstado ?>">
                                        <span class="estado-punto"></span>
                                        <?= htmlspecialchars(
                                            $estado,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=detalle&id=<?= (int) $s["id_solicitud"] ?>"
                                        class="btn btn-secondary btn-detalle">
                                        <i class="fa-solid fa-eye"></i>
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>

</main>

<style>
    .tabla-responsive {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: 14px;
    }

    .tabla-solicitudes {
        width: 100%;
        border-collapse: collapse;
        min-width: 650px;
    }

    .tabla-solicitudes thead {
        background: rgba(255, 255, 255, 0.03);
    }

    .tabla-solicitudes th {
        padding: 15px 18px;
        text-align: left;
        color: var(--text-secondary);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .tabla-solicitudes td {
        padding: 16px 18px;
        color: var(--text);
        font-size: 13px;
        border-bottom: 1px solid var(--border);
    }

    .tabla-solicitudes tbody tr:last-child td {
        border-bottom: none;
    }

    .tabla-solicitudes tbody tr {
        transition: background 0.2s ease;
    }

    .tabla-solicitudes tbody tr:hover {
        background: rgba(255, 255, 255, 0.025);
    }

    .estado-solicitud {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .estado-punto {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
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

    .btn-detalle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    @media (max-width: 700px) {
        .page-header {
            align-items: flex-start;
            gap: 15px;
        }

        .page-header > .btn {
            flex-shrink: 0;
        }

        .tabla-solicitudes th,
        .tabla-solicitudes td {
            padding: 13px;
        }
    }
</style>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>
