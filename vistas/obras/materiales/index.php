<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = $solicitudes ?? [];
$obra = $obra ?? [];

?>

<main class="content">

    <!-- ENCABEZADO -->

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Solicitudes de materiales
            </h1>

            <p class="page-subtitle">
                <?= htmlspecialchars($obra["nombre_obra"] ?? "Obra") ?>
            </p>

        </div>
<div style="display:flex; gap:10px;">

    <?php if (
        isset($_SESSION["usuario"]["rol"]) &&
        $_SESSION["usuario"]["rol"] === "Jefe de Obra"
    ): ?>

        <a
            href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=crear&id_obra=<?= $obra["id_obra"] ?>"
            class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            Nueva solicitud
        </a>

    <?php endif; ?>

    <a
        href="/empresa_constructora/vistas/obras/ver.php?id=<?= $obra["id_obra"] ?>"
        class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Volver
    </a>

</div>

    </div>


    <!-- LISTADO -->

    <div class="card">

        <?php if (empty($solicitudes)): ?>

            <div style="
                padding:50px;
                text-align:center;
                color:#888;
            ">

                <h3>No hay solicitudes</h3>

                <p>
                    Todavía no se realizaron solicitudes de materiales
                    para esta obra.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>Solicitud</th>

                            <th>Fecha</th>

                            <th>Materiales</th>

                            <th>Estado</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($solicitudes as $solicitud): ?>

                            <tr>

                                <!-- ID -->

                                <td>

                                    <strong>
                                        #<?= htmlspecialchars(
                                                $solicitud["id_solicitud"]
                                            ) ?>
                                    </strong>

                                </td>


                                <!-- FECHA -->

                                <td>

                                    <?= date(
                                        "d/m/Y",
                                        strtotime($solicitud["fecha"])
                                    ) ?>

                                </td>


                                <!-- CANTIDAD DE MATERIALES -->

                                <td>

                                    <?= htmlspecialchars(
                                        $solicitud["cantidad_materiales"]
                                    ) ?>

                                    <?= $solicitud["cantidad_materiales"] == 1
                                        ? "material"
                                        : "materiales"
                                    ?>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <?php

                                    $estado = $solicitud["estado"];

                                    switch ($estado) {

                                        case "Pendiente":
                                            $claseEstado = "badge-warning";
                                            break;

                                        case "Aprobada":
                                            $claseEstado = "badge-success";
                                            break;

                                        case "Rechazada":
                                            $claseEstado = "badge-danger";
                                            break;

                                        case "Entregada":
                                            $claseEstado = "badge-info";
                                            break;

                                        default:
                                            $claseEstado = "badge-secondary";
                                            break;
                                    }

                                    ?>

                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>

                                </td>


                                <!-- ACCIONES -->

                                <td>

                                    <a
                                        href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=detalle&id=<?= $solicitud["id_solicitud"] ?>"
                                        class="btn btn-secondary btn-sm">
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
    .table-responsive {
        overflow-x: auto;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
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
    }
</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>