<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

$puedeConsultar = in_array($rol, [2, 3, 5], true);
$puedeGestionar = ($rol === 5);

if (!$puedeConsultar) {
    http_response_code(403);
    exit("No tenés permiso para ver estas solicitudes.");
}

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = isset($solicitudes) && is_array($solicitudes)
    ? $solicitudes
    : [];

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                Solicitudes de herramientas
            </h1>

            <p class="page-subtitle">
                Supervisión y gestión de solicitudes de herramientas de todas las obras.
            </p>
        </div>

    </div>

    <?php if (!empty($_SESSION["success"])): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION["success"]) ?>
        </div>

        <?php unset($_SESSION["success"]); ?>

    <?php endif; ?>

    <?php if (!empty($_SESSION["error"])): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION["error"]) ?>
        </div>

        <?php unset($_SESSION["error"]); ?>

    <?php endif; ?>

    <div class="card">

        <div class="card-header">

            <div>
                <h2>Solicitudes registradas</h2>

                <p class="text-muted">
                    Consultá las solicitudes, revisá sus estados y gestioná las entregas autorizadas.
                </p>
            </div>

            <span class="total-solicitudes">
                Total: <?= count($solicitudes) ?>
            </span>

        </div>

        <?php if (empty($solicitudes)): ?>

            <div style="
                padding: 50px;
                text-align: center;
                color: #888;
            ">

                <h3>No hay solicitudes</h3>

                <p>
                    Todavía no existen solicitudes de herramientas registradas.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>
                        <tr>
                            <th>Solicitud</th>
                            <th>Obra</th>
                            <th>Solicitante</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($solicitudes as $solicitud): ?>

                            <?php

                            $idSolicitud = (int)(
                                $solicitud["id_solicitud"] ?? 0
                            );

                            $estado = $solicitud["estado"] ?? "";

                            $fecha = $solicitud["fecha"] ?? "";

                            if ($estado === "Pendiente") {
                                $claseEstado = "badge-warning";
                            } elseif ($estado === "Aprobada") {
                                $claseEstado = "badge-success";
                            } elseif ($estado === "Rechazada") {
                                $claseEstado = "badge-danger";
                            } elseif ($estado === "Entregada") {
                                $claseEstado = "badge-info";
                            } else {
                                $claseEstado = "badge-secondary";
                            }

                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        #<?= $idSolicitud ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $solicitud["nombre_obra"] ?? "Sin obra"
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $solicitud["solicitante"] ?? "No disponible"
                                    ) ?>
                                </td>

                                <td>
                                    <?php if (!empty($fecha)): ?>
                                        <?= date(
                                            "d/m/Y",
                                            strtotime($fecha)
                                        ) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>

                                <td class="observaciones">
                                    <?= htmlspecialchars(
                                        $solicitud["observaciones"] ?? "-"
                                    ) ?>
                                </td>

                                <td>


                                    <div class="acciones">

                                        <?php if (
                                            $puedeGestionar &&
                                            $estado === "Pendiente"
                                        ): ?>

                                            <form
                                                method="POST"
                                                action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=aprobar">

                                                <input
                                                    type="hidden"
                                                    name="id_solicitud"
                                                    value="<?= $idSolicitud ?>">

                                                <button type="submit" class="btn btn-success btn-sm">
                                                    Aprobar
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=rechazar"
                                                onsubmit="return confirm('¿Rechazar esta solicitud?')">

                                                <input
                                                    type="hidden"
                                                    name="id_solicitud"
                                                    value="<?= $idSolicitud ?>">

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Rechazar
                                                </button>
                                            </form>

                                        <?php elseif (
                                            $puedeGestionar &&
                                            $estado === "Aprobada"
                                        ): ?>

                                            <form
                                                method="POST"
                                                action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=entregar"
                                                onsubmit="return confirm('¿Confirmás la entrega de estas herramientas?')">

                                                <input
                                                    type="hidden"
                                                    name="id_solicitud"
                                                    value="<?= $idSolicitud ?>">

                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Registrar entrega
                                                </button>
                                            </form>

                                        <?php endif; ?>

                                        <a
                                            href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=detalle&id=<?= $idSolicitud ?>"
                                            class="btn btn-secondary btn-sm">
                                            Ver detalle
                                        </a>

                                    </div>


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
    .text-muted {
        color: #8b8f98;
        font-size: 14px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card-header h2 {
        margin: 0;
    }

    .card-header p {
        margin: 5px 0 0;
    }

    .total-solicitudes {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 20px;
        background: rgba(245, 158, 11, .15);
        color: #f59e0b;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 14px 12px;
        text-align: left;
        vertical-align: middle;
    }

    .table th {
        white-space: nowrap;
    }

    .observaciones {
        min-width: 150px;
        max-width: 260px;
        overflow-wrap: anywhere;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-warning {
        background: rgba(245, 158, 11, .15);
        color: #f59e0b;
    }

    .badge-success {
        background: rgba(34, 197, 94, .15);
        color: #22c55e;
    }

    .badge-danger {
        background: rgba(239, 68, 68, .15);
        color: #ef4444;
    }

    .badge-info {
        background: rgba(59, 130, 246, .15);
        color: #3b82f6;
    }

    .badge-secondary {
        background: rgba(148, 163, 184, .15);
        color: #94a3b8;
    }

    .btn-sm {
        padding: 7px 12px;
        font-size: 13px;
        white-space: nowrap;
    }

    .acciones {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        min-width: 150px;
    }

    .acciones form {
        margin: 0;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: rgba(34, 197, 94, .12);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, .25);
    }

    .alert-danger {
        background: rgba(239, 68, 68, .12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, .25);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>