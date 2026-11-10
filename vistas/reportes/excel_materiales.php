<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$materiales = $reporte->materiales();
$movimientos = $reporte->movimientosMateriales(300);

$totalMateriales = count($materiales);
$activos = 0;
$criticos = 0;
$sinStock = 0;
$pedidosSinStock = 0;
$criticosLista = [];

foreach ($materiales as $material) {
    if ((int)$material["estado"] === 1) {
        $activos++;
    }
    if ($material["estado_stock"] === "Crítico") {
        $criticos++;
    }
    if ($material["estado_stock"] === "Sin stock") {
        $sinStock++;
    }
    if (!$material["alcanza_pedidos"]) {
        $pedidosSinStock++;
    }
    if ($material["estado_stock"] !== "Normal" && (int)$material["estado"] === 1) {
        $criticosLista[] = $material;
    }
}

$ingresos = count(array_filter($movimientos, fn($m) => $m["tipo"] === "INGRESO"));
$egresos = count($movimientos) - $ingresos;

// Los 10 materiales con más salidas (egresos + entregas)
$masUsados = $materiales;
usort($masUsados, fn($a, $b) => ((float)$b["total_egresos"] + (float)$b["total_entregado"])
    <=> ((float)$a["total_egresos"] + (float)$a["total_entregado"]));
$masUsados = array_slice(array_filter($masUsados, fn($m) => ((float)$m["total_egresos"] + (float)$m["total_entregado"]) > 0), 0, 10);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Materiales</h1>
            <p>Stock, stock mínimo, movimientos de ingreso y egreso, entregas a obras y pedidos pendientes.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Materiales</p>
            <h3><span data-contador="tablaMateriales"><?= $totalMateriales ?></span> de <?= $totalMateriales ?></h3>
            <small><?= $activos ?> activos</small>
        </div>
        <div class="alert alert-warning">
            <p>Stock crítico</p>
            <h3><?= $criticos ?></h3>
            <small>En el mínimo o por debajo</small>
        </div>
        <div class="alert alert-danger">
            <p>Sin stock</p>
            <h3><?= $sinStock ?></h3>
            <small><?= $pedidosSinStock ?> no alcanzan para los pedidos pendientes</small>
        </div>
        <div class="alert alert-info">
            <p>Movimientos recientes</p>
            <h3><?= count($movimientos) ?></h3>
            <small><?= $ingresos ?> ingresos · <?= $egresos ?> egresos</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte de Materiales"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaMateriales" placeholder="Buscar material...">
                <select class="filter" data-filtro="tablaMateriales" data-campo="estado" data-exportar-estado>
                    <option value="">Todo el stock</option>
                    <option value="Normal">Stock normal</option>
                    <option value="Crítico">Stock crítico</option>
                    <option value="Sin stock">Sin stock</option>
                </select>
                <select class="filter" data-filtro="tablaMateriales" data-campo="activo">
                    <option value="">Activos e inactivos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
            <?php botonesReporte("materiales", "tablaMateriales"); ?>
        </div>

        <table class="table" id="tablaMateriales">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Unidad</th>
                    <th class="numero">Stock</th>
                    <th class="numero">Mínimo</th>
                    <th class="numero">Ingresos</th>
                    <th class="numero">Egresos</th>
                    <th class="numero">Entregado a obras</th>
                    <th class="numero">Pedido pendiente</th>
                    <th>Último movimiento</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalMateriales === 0) { ?>
                    <tr class="fila-vacia"><td colspan="10">No hay materiales registrados.</td></tr>
                <?php } ?>

                <?php foreach ($materiales as $material) { ?>
                    <tr data-estado="<?= e($material["estado_stock"]) ?>" data-activo="<?= (int)$material["estado"] ?>">
                        <td>
                            <strong><?= e($material["nombre_material"]) ?></strong>
                            <?php if ((int)$material["estado"] !== 1) { ?>
                                <span class="badge badge-secondary">Inactivo</span>
                            <?php } ?>
                            <br><span class="texto-secundario"><?= e($material["descripcion"]) ?></span>
                        </td>
                        <td><?= e($material["unidad_medida"]) ?></td>
                        <td class="numero"><strong><?= e(Reporte::numero($material["stock"])) ?></strong></td>
                        <td class="numero"><?= e(Reporte::numero($material["stock_minimo"])) ?></td>
                        <td class="numero"><?= e(Reporte::numero($material["total_ingresos"])) ?></td>
                        <td class="numero"><?= e(Reporte::numero($material["total_egresos"])) ?></td>
                        <td class="numero"><?= e(Reporte::numero($material["total_entregado"])) ?></td>
                        <td class="numero">
                            <?= e(Reporte::numero($material["cantidad_pedida_pendiente"])) ?>
                            <?php if (!$material["alcanza_pedidos"]) { ?>
                                <br><span class="badge badge-danger">No alcanza</span>
                            <?php } ?>
                        </td>
                        <td><?= e(Reporte::fecha($material["ultimo_movimiento"], true)) ?></td>
                        <td><?= badge($material["estado_stock"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if (count($criticosLista) > 0) { ?>
        <div class="table-container reporte-seccion">
            <div class="table-header">
                <div>
                    <h2><i class="fa-solid fa-triangle-exclamation"></i> Materiales para reponer</h2>
                    <small>Materiales activos con stock crítico o sin stock.</small>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th class="numero">Stock</th>
                        <th class="numero">Mínimo</th>
                        <th class="numero">Faltante para el mínimo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criticosLista as $material) {
                        $faltante = max(0, (float)$material["stock_minimo"] - (float)$material["stock"]); ?>
                        <tr>
                            <td><?= e($material["nombre_material"]) ?></td>
                            <td class="numero"><?= e(Reporte::numero($material["stock"])) ?> <?= e($material["unidad_medida"]) ?></td>
                            <td class="numero"><?= e(Reporte::numero($material["stock_minimo"])) ?></td>
                            <td class="numero"><?= e(Reporte::numero($faltante)) ?></td>
                            <td><?= badge($material["estado_stock"]) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <div class="table-container reporte-seccion">
        <div class="table-header">
            <div>
                <h2><i class="fa-solid fa-right-left"></i> Movimientos de stock</h2>
                <small>Últimos <?= count($movimientos) ?> ingresos y egresos registrados.</small>
            </div>
        </div>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaMovimientos" placeholder="Buscar material, obra o usuario...">
                <select class="filter" data-filtro="tablaMovimientos" data-campo="estado" data-exportar-estado>
                    <option value="">Ingresos y egresos</option>
                    <option value="INGRESO">Ingresos</option>
                    <option value="EGRESO">Egresos</option>
                </select>
            </div>
            <?php botonesReporte("movimientos", "tablaMovimientos"); ?>
        </div>

        <table class="table" id="tablaMovimientos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Material</th>
                    <th>Tipo</th>
                    <th class="numero">Cantidad</th>
                    <th>Obra</th>
                    <th>Usuario</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($movimientos) === 0) { ?>
                    <tr class="fila-vacia"><td colspan="7">No hay movimientos registrados.</td></tr>
                <?php } ?>
                <?php foreach ($movimientos as $mov) { ?>
                    <tr data-estado="<?= e($mov["tipo"]) ?>">
                        <td><?= e(Reporte::fecha($mov["fecha"], true)) ?></td>
                        <td><?= e($mov["nombre_material"]) ?></td>
                        <td><?= badge($mov["tipo"]) ?></td>
                        <td class="numero"><?= e(Reporte::numero($mov["cantidad"])) ?> <?= e($mov["unidad_medida"]) ?></td>
                        <td><?= e($mov["nombre_obra"] ?: "-") ?></td>
                        <td><?= e($mov["usuario"]) ?></td>
                        <td><?= e($mov["observacion"] ?: "-") ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if (count($masUsados) > 0) { ?>
        <div class="graficos-reporte no-print">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Materiales más usados</h2>
                        <p>Egresos más entregas a obras (cantidad en su unidad).</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoUsados"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Estado del stock</h2>
                        <p>Materiales con stock normal, crítico y sin stock.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoStock"></canvas></div>
            </div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Chart === "undefined") return;

        const usados = document.getElementById("graficoUsados");
        if (usados) {
            new Chart(usados, {
                type: "bar",
                data: {
                    labels: <?= json_encode(array_values(array_column($masUsados, "nombre_material")), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: "Cantidad usada",
                        data: <?= json_encode(array_values(array_map(fn($m) => (float)$m["total_egresos"] + (float)$m["total_entregado"], $masUsados))) ?>,
                        backgroundColor: "rgba(245, 180, 0, 0.7)"
                    }]
                },
                options: { indexAxis: "y", responsive: true, maintainAspectRatio: false }
            });
        }

        const stock = document.getElementById("graficoStock");
        if (stock) {
            new Chart(stock, {
                type: "doughnut",
                data: {
                    labels: ["Normal", "Crítico", "Sin stock"],
                    datasets: [{
                        data: [<?= $totalMateriales - $criticos - $sinStock ?>, <?= $criticos ?>, <?= $sinStock ?>],
                        backgroundColor: ["#22c55e", "#f5b400", "#ef4444"]
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    });
</script>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";