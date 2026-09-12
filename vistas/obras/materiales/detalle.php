<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitud = $solicitud ?? [];
$obra = $obra ?? [];
$detalle = $detalle ?? [];

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                Solicitud #<?= htmlspecialchars($solicitud["id_solicitud"]) ?>
            </h1>

            <p class="page-subtitle">
                <?= htmlspecialchars($obra["nombre_obra"] ?? "Obra") ?>
            </p>
        </div>

<div style="display:flex; gap:10px;">

    <a
        href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= $solicitud["id_obra"] ?>"
        class="btn btn-secondary">
        Volver a solicitudes
    </a>

    <a
        href="/empresa_constructora/vistas/obras/ver.php?id=<?= $solicitud["id_obra"] ?>"
        class="btn btn-secondary">
        Volver a obra
    </a>

</div>

    </div>


    <!-- INFORMACIÓN DE LA SOLICITUD -->

    <div class="card" style="margin-bottom:25px;">

        <div style="
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:20px;
        ">

            <div>

                <span class="text-muted">
                    Solicitud
                </span>

                <h3 style="margin-top:5px;">
                    #<?= htmlspecialchars($solicitud["id_solicitud"]) ?>
                </h3>

            </div>


            <div>

                <span class="text-muted">
                    Fecha
                </span>

                <h3 style="margin-top:5px;">

                    <?= date(
                        "d/m/Y",
                        strtotime($solicitud["fecha"])
                    ) ?>

                </h3>

            </div>


            <div>

                <span class="text-muted">
                    Estado
                </span>

                <div style="margin-top:8px;">

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

                </div>

            </div>

        </div>

    </div>


    <!-- MATERIALES SOLICITADOS -->

    <div class="card">

        <div class="card-header">

            <div>

                <h2>
                    Materiales solicitados
                </h2>

                <p class="text-muted">
                    Detalle de los materiales incluidos en esta solicitud.
                </p>

            </div>

        </div>


        <?php if (empty($detalle)): ?>

            <div style="
                padding:40px;
                text-align:center;
                color:#777;
            ">

                <h3>
                    No hay materiales
                </h3>

                <p>
                    Esta solicitud no contiene materiales.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Material
                            </th>

                            <th>
                                Unidad
                            </th>

                            <th>
                                Cantidad solicitada
                            </th>

                            <th>
                                Stock actual
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($detalle as $material): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $material["nombre_material"]
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $material["unidad_medida"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= number_format(
                                        $material["cantidad"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                </td>


                                <td>

                                    <?= number_format(
                                        $material["stock"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $material["unidad_medida"]
                                    ) ?>

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

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .card-header h2 {
        margin: 0;
    }

    .card-header p {
        margin: 5px 0 0;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>