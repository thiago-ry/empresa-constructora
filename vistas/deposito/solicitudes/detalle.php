<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("materiales");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitud = $solicitud ?? [];
$obra = $obra ?? [];
$detalle = $detalle ?? [];

$estado = $solicitud["estado"] ?? "Pendiente";

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

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                Solicitud #<?= htmlspecialchars($solicitud["id_solicitud"] ?? "") ?>
            </h1>

            <p class="page-subtitle">
                Detalle de solicitud de materiales
            </p>
        </div>

        <div style="display:flex;gap:10px;">

            <?php if ($estado === "Pendiente"): ?>

                <a
                    href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=aprobar&id=<?= $solicitud["id_solicitud"] ?>"
                    class="btn btn-primary"
                    onclick="return confirm('¿Aprobar esta solicitud?');">
                    <i class="fa-solid fa-check"></i>
                    Aprobar
                </a>

                <a
                    href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=rechazar&id=<?= $solicitud["id_solicitud"] ?>"
                    class="btn btn-danger"
                    onclick="return confirm('¿Rechazar esta solicitud?');">
                    <i class="fa-solid fa-xmark"></i>
                    Rechazar
                </a>

            <?php elseif ($estado === "Aprobada"): ?>

                <a
                    href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=entregar&id=<?= $solicitud["id_solicitud"] ?>"
                    class="btn btn-primary">
                    <i class="fa-solid fa-truck"></i>
                    Registrar entrega
                </a>

            <?php endif; ?>

            <a
                href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=listar"
                class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Volver
            </a>

        </div>

    </div>


    <!-- INFORMACIÓN DE LA SOLICITUD -->

    <div class="stats-grid">

        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <span>Obra</span>
                <strong>
                    <?= htmlspecialchars($obra["nombre_obra"] ?? "Sin obra") ?>
                </strong>
            </div>

        </div>


        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-calendar"></i>
            </div>

            <div>
                <span>Fecha</span>
                <strong>
                    <?= !empty($solicitud["fecha"])
                        ? date("d/m/Y", strtotime($solicitud["fecha"]))
                        : "-"
                    ?>
                </strong>
            </div>

        </div>


        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-circle-info"></i>
            </div>

            <div>
                <span>Estado</span>

                <strong>
                    <span class="badge <?= $claseEstado ?>">
                        <?= htmlspecialchars($estado) ?>
                    </span>
                </strong>

            </div>

        </div>


        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>

            <div>
                <span>Materiales</span>

                <strong>
                    <?= count($detalle) ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- MATERIALES -->

    <div class="card">

        <div class="card-header-custom">

            <div>
                <h2>Materiales solicitados</h2>

                <p>
                    Revisá las cantidades solicitadas y el stock disponible.
                </p>
            </div>

        </div>


        <?php if (empty($detalle)): ?>

            <div class="empty-state">

                <i class="fa-solid fa-box-open"></i>

                <h3>No hay materiales</h3>

                <p>
                    Esta solicitud no contiene materiales.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>
                            <th>Material</th>
                            <th>Unidad</th>
                            <th>Cantidad solicitada</th>
                            <th>Stock actual</th>
                            <th>Disponibilidad</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($detalle as $material): ?>

                            <?php

                            $solicitado = (float)$material["cantidad"];
                            $stock = (float)$material["stock"];

                            $hayStock = $stock >= $solicitado;

                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($material["nombre_material"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($material["unidad_medida"]) ?>
                                </td>

                                <td>
                                    <?= rtrim(rtrim(number_format($solicitado, 2, ",", "."), "0"), ",") ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= rtrim(rtrim(number_format($stock, 2, ",", "."), "0"), ",") ?>
                                    </strong>
                                </td>

                                <td>

                                    <?php if ($hayStock): ?>

                                        <span class="stock-ok">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Disponible
                                        </span>

                                    <?php else: ?>

                                        <span class="stock-no">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            Stock insuficiente
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>


    <!-- AVISO DE STOCK -->

    <?php if ($estado === "Pendiente" && !empty($detalle)): ?>

        <?php

        $stockInsuficiente = false;

        foreach ($detalle as $material) {

            if ((float)$material["stock"] < (float)$material["cantidad"]) {
                $stockInsuficiente = true;
                break;
            }

        }

        ?>

        <?php if ($stockInsuficiente): ?>

            <div class="alert-stock">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <div>

                    <strong>Stock insuficiente</strong>

                    <p>
                        Uno o más materiales no cuentan con stock suficiente
                        para cubrir esta solicitud.
                    </p>

                </div>

            </div>

        <?php else: ?>

            <div class="alert-success">

                <i class="fa-solid fa-circle-check"></i>

                <div>

                    <strong>Stock disponible</strong>

                    <p>
                        Hay stock suficiente para todos los materiales
                        solicitados.
                    </p>

                </div>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</main>


<style>

.stats-grid {
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:18px;
    margin-bottom:20px;
}

.info-card {
    display:flex;
    align-items:center;
    gap:15px;
}

.info-icon {
    width:46px;
    height:46px;
    border-radius:12px;
    background:rgba(244,180,0,.12);
    color:#f4b400;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}

.info-card span {
    display:block;
}

.info-card > div:last-child > span:first-child {
    color:#8b98a8;
    font-size:13px;
    margin-bottom:4px;
}

.info-card strong {
    font-size:16px;
}

.card-header-custom {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.card-header-custom h2 {
    margin:0;
    font-size:20px;
}

.card-header-custom p {
    margin:5px 0 0;
    color:#8b98a8;
    font-size:14px;
}

.table-responsive {
    overflow-x:auto;
}

.badge {
    display:inline-flex;
    align-items:center;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.badge-warning {
    background:rgba(245,158,11,.15);
    color:#f59e0b;
}

.badge-success {
    background:rgba(34,197,94,.15);
    color:#22c55e;
}

.badge-danger {
    background:rgba(239,68,68,.15);
    color:#ef4444;
}

.badge-info {
    background:rgba(59,130,246,.15);
    color:#3b82f6;
}

.badge-secondary {
    background:rgba(148,163,184,.15);
    color:#94a3b8;
}

.stock-ok {
    display:inline-flex;
    align-items:center;
    gap:7px;
    color:#22c55e;
    font-weight:600;
    font-size:13px;
}

.stock-no {
    display:inline-flex;
    align-items:center;
    gap:7px;
    color:#ef4444;
    font-weight:600;
    font-size:13px;
}

.alert-stock,
.alert-success {
    display:flex;
    align-items:flex-start;
    gap:14px;
    padding:18px 20px;
    border-radius:14px;
    margin-top:20px;
}

.alert-stock {
    background:rgba(239,68,68,.10);
    border:1px solid rgba(239,68,68,.25);
    color:#ef4444;
}

.alert-success {
    background:rgba(34,197,94,.10);
    border:1px solid rgba(34,197,94,.25);
    color:#22c55e;
}

.alert-stock i,
.alert-success i {
    font-size:20px;
    margin-top:2px;
}

.alert-stock strong,
.alert-success strong {
    display:block;
    margin-bottom:4px;
}

.alert-stock p,
.alert-success p {
    margin:0;
    color:#9ca3af;
}

.empty-state {
    padding:50px;
    text-align:center;
    color:#888;
}

.empty-state i {
    font-size:40px;
    margin-bottom:15px;
}

.empty-state h3 {
    margin-bottom:5px;
}

.empty-state p {
    margin:0;
}

@media (max-width:1000px) {

    .stats-grid {
        grid-template-columns:repeat(2, 1fr);
    }

}

@media (max-width:600px) {

    .stats-grid {
        grid-template-columns:1fr;
    }

    .page-header {
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

}

</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>