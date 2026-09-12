<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = $solicitudes ?? [];

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Solicitudes de materiales
            </h1>

            <p class="page-subtitle">
                Supervisión general de solicitudes de todas las obras.
            </p>

        </div>

    </div>


    <div class="card">

        <?php if (empty($solicitudes)): ?>

            <div style="
                padding:50px;
                text-align:center;
                color:#888;
            ">

                <h3>
                    No hay solicitudes
                </h3>

                <p>
                    Todavía no existen solicitudes de materiales registradas.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Solicitud
                            </th>

                            <th>
                                Obra
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Materiales
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($solicitudes as $solicitud): ?>

                            <tr>

                                <td>

                                    <strong>
                                        #<?= htmlspecialchars(
                                            $solicitud["id_solicitud"]
                                        ) ?>
                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $solicitud["nombre_obra"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= date(
                                        "d/m/Y",
                                        strtotime($solicitud["fecha"])
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $solicitud["cantidad_materiales"]
                                    ) ?>

                                    <?=
                                        $solicitud["cantidad_materiales"] == 1
                                            ? "material"
                                            : "materiales"
                                    ?>

                                </td>


                                <td>

                                    <?php

                                    $estado = $solicitud["estado"];

                                    if ($estado == "Pendiente") {

                                        $claseEstado = "badge-warning";

                                    } elseif ($estado == "Aprobada") {

                                        $claseEstado = "badge-success";

                                    } elseif ($estado == "Rechazada") {

                                        $claseEstado = "badge-danger";

                                    } elseif ($estado == "Entregada") {

                                        $claseEstado = "badge-info";

                                    } else {

                                        $claseEstado = "badge-secondary";

                                    }

                                    ?>

                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>

                                </td>


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