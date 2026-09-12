<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("materiales");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$entrega = $entrega ?? [];
$detalleEntrega = $detalleEntrega ?? [];
$obra = $obra ?? [];

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Entrega #<?= htmlspecialchars($entrega["id_entrega"] ?? "") ?>
            </h1>

            <p class="page-subtitle">
                Detalle de materiales entregados
            </p>

        </div>

        <a
            href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=historial"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver al historial

        </a>

    </div>


    <div class="stats-grid">

        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>

                <span>Obra</span>

                <strong>
                    <?= htmlspecialchars(
                        $obra["nombre_obra"] ?? "Sin obra"
                    ) ?>
                </strong>

            </div>

        </div>


        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>

            <div>

                <span>Solicitud</span>

                <strong>
                    #<?= htmlspecialchars(
                        $entrega["id_solicitud"]
                    ) ?>
                </strong>

            </div>

        </div>


        <div class="card info-card">

            <div class="info-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div>

                <span>Responsable</span>

                <strong>
                    <?= htmlspecialchars(
                        $entrega["nombre"] . " " . $entrega["apellido"]
                    ) ?>
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

                    <?= date(
                        "d/m/Y H:i",
                        strtotime($entrega["fecha"])
                    ) ?>

                </strong>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-header-custom">

            <div>

                <h2>Materiales entregados</h2>

                <p>
                    Registro de los materiales descontados del inventario.
                </p>

            </div>

        </div>


        <?php if (empty($detalleEntrega)): ?>

            <div class="empty-state">

                <i class="fa-solid fa-box-open"></i>

                <h3>No hay detalles</h3>

                <p>
                    Esta entrega no tiene materiales registrados.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>
                            <th>Material</th>
                            <th>Cantidad entregada</th>
                            <th>Unidad</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($detalleEntrega as $material): ?>

                            <tr>

                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $material["nombre_material"]
                                        ) ?>
                                    </strong>

                                </td>

                                <td>

                                    <?= rtrim(
                                        rtrim(
                                            number_format(
                                                (float)$material["cantidad_entregada"],
                                                2,
                                                ",",
                                                "."
                                            ),
                                            "0"
                                        ),
                                        ","
                                    ) ?>

                                </td>

                                <td>

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


        <?php if (!empty($entrega["observaciones"])): ?>

            <div class="observaciones">

                <strong>
                    <i class="fa-solid fa-comment"></i>
                    Observaciones
                </strong>

                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $entrega["observaciones"]
                        )
                    ) ?>
                </p>

            </div>

        <?php endif; ?>

    </div>

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
    color:#8b98a8;
    font-size:13px;
    margin-bottom:4px;
}

.info-card strong {
    font-size:15px;
}

.card-header-custom {
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

.observaciones {
    margin-top:25px;
    padding:18px;
    border-radius:12px;
    background:rgba(244,180,0,.07);
    border:1px solid rgba(244,180,0,.15);
}

.observaciones strong {
    display:block;
    color:#f4b400;
    margin-bottom:8px;
}

.observaciones p {
    margin:0;
    color:#cbd5e1;
    line-height:1.6;
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

@media (max-width:1000px) {

    .stats-grid {
        grid-template-columns:repeat(2, 1fr);
    }

}

@media (max-width:600px) {

    .stats-grid {
        grid-template-columns:1fr;
    }

}

</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>