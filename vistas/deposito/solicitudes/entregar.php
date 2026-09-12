<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("materiales");

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
                Registrar entrega
            </h1>

            <p class="page-subtitle">
                Solicitud #<?= htmlspecialchars($solicitud["id_solicitud"] ?? "") ?>
                -
                <?= htmlspecialchars($obra["nombre_obra"] ?? "Obra") ?>
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=detalle&id=<?= $solicitud["id_solicitud"] ?>"
            class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>

    </div>


    <form
        method="POST"
        action="/empresa_constructora/controladores/EntregaMaterialController.php?accion=guardarEntrega"
        id="formEntrega">

        <input
            type="hidden"
            name="id_solicitud"
            value="<?= htmlspecialchars($solicitud["id_solicitud"] ?? "") ?>">


        <!-- INFORMACIÓN -->

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
                    <span>Fecha de solicitud</span>
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
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div>
                    <span>Estado</span>
                    <strong class="estado-aprobada">
                        Aprobada
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
                    <h2>Materiales a entregar</h2>

                    <p>
                        Indicá la cantidad que se entrega de cada material.
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
                                <th>Solicitado</th>
                                <th>Stock disponible</th>
                                <th>Cantidad a entregar</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($detalle as $material): ?>

                                <?php

                                $solicitado = (float)$material["cantidad"];
                                $stock = (float)$material["stock"];

                                ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($material["nombre_material"]) ?>
                                        </strong>

                                        <input
                                            type="hidden"
                                            name="material[]"
                                            value="<?= htmlspecialchars($material["id_material"]) ?>">

                                    </td>


                                    <td>
                                        <?= htmlspecialchars($material["unidad_medida"]) ?>
                                    </td>


                                    <td>

                                        <?= rtrim(
                                            rtrim(
                                                number_format(
                                                    $solicitado,
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

                                        <strong>
                                            <?= rtrim(
                                                rtrim(
                                                    number_format(
                                                        $stock,
                                                        2,
                                                        ",",
                                                        "."
                                                    ),
                                                    "0"
                                                ),
                                                ","
                                            ) ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <input
                                            type="number"
                                            name="cantidad_entregada[]"
                                            class="input-cantidad"
                                            min="0"
                                            max="<?= htmlspecialchars($solicitado) ?>"
                                            step="0.01"
                                            value="<?= htmlspecialchars($solicitado) ?>"
                                            data-solicitado="<?= htmlspecialchars($solicitado) ?>"
                                            data-stock="<?= htmlspecialchars($stock) ?>"
                                            required>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- OBSERVACIONES -->

                <div class="form-group">

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        rows="4"
                        placeholder="Agregá alguna observación sobre la entrega..."></textarea>

                </div>


                <!-- RESUMEN -->

                <div class="delivery-info">

                    <div>

                        <i class="fa-solid fa-circle-info"></i>

                        <div>

                            <strong>Importante</strong>

                            <p>
                                El stock se descontará automáticamente al confirmar
                                la entrega.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- BOTONES -->

                <div class="form-actions">

                    <a
                        href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=detalle&id=<?= $solicitud["id_solicitud"] ?>"
                        class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="fa-solid fa-truck"></i>
                        Confirmar entrega
                    </button>

                </div>

            <?php endif; ?>

        </div>

    </form>

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

.info-card > div:last-child > span {
    display:block;
    color:#8b98a8;
    font-size:13px;
    margin-bottom:4px;
}

.info-card strong {
    font-size:16px;
}

.estado-aprobada {
    color:#22c55e;
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

.input-cantidad {
    width:130px;
    padding:10px 12px;
    border:1px solid rgba(255,255,255,.10);
    border-radius:10px;
    background:#0b1320;
    color:#fff;
    font-size:14px;
    outline:none;
}

.input-cantidad:focus {
    border-color:#f4b400;
}

.form-group {
    margin-top:25px;
}

.form-group label {
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

.form-group textarea {
    width:100%;
    box-sizing:border-box;
    padding:12px 14px;
    border:1px solid rgba(255,255,255,.10);
    border-radius:10px;
    background:#0b1320;
    color:#fff;
    font-family:inherit;
    resize:vertical;
    outline:none;
}

.form-group textarea:focus {
    border-color:#f4b400;
}

.delivery-info {
    margin-top:20px;
    padding:16px 18px;
    border-radius:12px;
    background:rgba(244,180,0,.08);
    border:1px solid rgba(244,180,0,.20);
}

.delivery-info > div {
    display:flex;
    gap:12px;
    align-items:flex-start;
}

.delivery-info i {
    color:#f4b400;
    font-size:18px;
    margin-top:2px;
}

.delivery-info strong {
    display:block;
    margin-bottom:4px;
}

.delivery-info p {
    margin:0;
    color:#9ca3af;
    font-size:14px;
}

.form-actions {
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:25px;
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

    .form-actions {
        flex-direction:column;
    }

    .form-actions .btn {
        width:100%;
    }

}

</style>


<script>

document.getElementById("formEntrega").addEventListener("submit", function(e) {

    var cantidades = document.querySelectorAll(".input-cantidad");

    for (var i = 0; i < cantidades.length; i++) {

        var input = cantidades[i];

        var cantidad = parseFloat(input.value);
        var solicitado = parseFloat(input.dataset.solicitado);
        var stock = parseFloat(input.dataset.stock);

        if (isNaN(cantidad) || cantidad <= 0) {

            alert("La cantidad entregada debe ser mayor a 0.");
            input.focus();
            e.preventDefault();
            return;

        }

        if (cantidad > solicitado) {

            alert(
                "No podés entregar más cantidad de la solicitada."
            );

            input.focus();
            e.preventDefault();
            return;

        }

        if (cantidad > stock) {

            alert(
                "No hay stock suficiente para entregar esa cantidad."
            );

            input.focus();
            e.preventDefault();
            return;

        }

    }

    if (!confirm("¿Confirmar la entrega de estos materiales?")) {

        e.preventDefault();
    }

});

</script>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>