<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$clientes = $reporte->clientes();

$totalClientes = count($clientes);
$activos = 0;
$conObras = 0;
$conObrasEnCurso = 0;
$totalPresupuestado = 0;

foreach ($clientes as $c) {
    if ((int)$c["estado"] === 1) {
        $activos++;
    }
    if ((int)$c["total_obras"] > 0) {
        $conObras++;
    }
    if ((int)$c["obras_en_curso"] > 0) {
        $conObrasEnCurso++;
    }
    $totalPresupuestado += (float)$c["total_presupuestado"];
}

// Clientes con más obras, para el gráfico
$ranking = array_filter($clientes, fn($c) => (int)$c["total_obras"] > 0);
usort($ranking, fn($a, $b) => (int)$b["total_obras"] <=> (int)$a["total_obras"]);
$ranking = array_slice($ranking, 0, 10);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Clientes</h1>
            <p>Clientes registrados, sus obras, el estado de cada una y el total presupuestado.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Clientes</p>
            <h3><span data-contador="tablaClientes"><?= $totalClientes ?></span> de <?= $totalClientes ?></h3>
            <small><?= $activos ?> activos · <?= $totalClientes - $activos ?> inactivos</small>
        </div>
        <div class="alert alert-info">
            <p>Con obras</p>
            <h3><?= $conObras ?></h3>
            <small><?= $totalClientes - $conObras ?> sin obras registradas</small>
        </div>
        <div class="alert alert-success">
            <p>Con obras en curso</p>
            <h3><?= $conObrasEnCurso ?></h3>
            <small>En planificación o en proceso</small>
        </div>
        <div class="alert alert-warning">
            <p>Total presupuestado</p>
            <h3><?= e(Reporte::moneda($totalPresupuestado)) ?></h3>
            <small>Última versión de cada obra</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte de Clientes"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaClientes" placeholder="Buscar cliente, documento u obra...">
                <select class="filter" data-filtro="tablaClientes" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                </select>
                <select class="filter" data-filtro="tablaClientes" data-campo="obras">
                    <option value="">Con y sin obras</option>
                    <option value="con">Con obras</option>
                    <option value="sin">Sin obras</option>
                </select>
            </div>
            <?php botonesReporte("clientes", "tablaClientes"); ?>
        </div>

        <table class="table" id="tablaClientes">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Contacto</th>
                    <th class="numero">Obras</th>
                    <th>Detalle</th>
                    <th class="numero">Presupuestado</th>
                    <th class="numero">Incidencias abiertas</th>
                    <th>Alta</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalClientes === 0) { ?>
                    <tr class="fila-vacia"><td colspan="9">No hay clientes registrados.</td></tr>
                <?php } ?>

                <?php foreach ($clientes as $c) {
                    $estado = (int)$c["estado"] === 1 ? "Activo" : "Inactivo"; ?>
                    <tr data-estado="<?= $estado ?>" data-obras="<?= (int)$c["total_obras"] > 0 ? "con" : "sin" ?>">
                        <td>
                            <strong><?= e($c["cliente"]) ?></strong>
                            <?php if (!empty($c["direccion"])) { ?>
                                <br><span class="texto-secundario"><?= e($c["direccion"]) ?></span>
                            <?php } ?>
                        </td>
                        <td><?= e($c["documento"] ?: "-") ?></td>
                        <td>
                            <?= e($c["correo"]) ?><br>
                            <span class="texto-secundario"><?= e($c["telefono"] ?: "sin teléfono") ?></span>
                        </td>
                        <td class="numero"><strong><?= (int)$c["total_obras"] ?></strong></td>
                        <td>
                            <?php if ((int)$c["total_obras"] === 0) { ?>
                                <span class="texto-secundario">Sin obras</span>
                            <?php } else { ?>
                                <?= e($c["obras"]) ?><br>
                                <span class="texto-secundario">
                                    <?= (int)$c["obras_en_curso"] ?> en curso · <?= (int)$c["obras_finalizadas"] ?> finalizadas
                                    <?php if (Reporte::fecha($c["primera_obra"]) !== "-") { ?>
                                        · desde <?= e(Reporte::fecha($c["primera_obra"])) ?>
                                    <?php } ?>
                                </span>
                            <?php } ?>
                        </td>
                        <td class="numero"><?= e(Reporte::moneda($c["total_presupuestado"])) ?></td>
                        <td class="numero"><?= (int)$c["incidencias_abiertas"] ?></td>
                        <td><?= e(Reporte::fecha($c["fecha_registro"])) ?></td>
                        <td><?= badge($estado) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if (count($ranking) > 0) { ?>
        <div class="card reporte-seccion no-print">
            <div class="card-header">
                <div>
                    <h2>Clientes con más obras</h2>
                    <p>Cantidad de obras por cliente (hasta 10 clientes).</p>
                </div>
            </div>
            <div class="grafico-caja"><canvas id="graficoClientes"></canvas></div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lienzo = document.getElementById("graficoClientes");
        if (!lienzo || typeof Chart === "undefined") return;

        new Chart(lienzo, {
            type: "bar",
            data: {
                labels: <?= json_encode(array_column($ranking, "cliente"), JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: "En curso",
                    data: <?= json_encode(array_map(fn($c) => (int)$c["obras_en_curso"], $ranking)) ?>,
                    backgroundColor: "rgba(59, 130, 246, 0.7)"
                }, {
                    label: "Finalizadas",
                    data: <?= json_encode(array_map(fn($c) => (int)$c["obras_finalizadas"], $ranking)) ?>,
                    backgroundColor: "rgba(34, 197, 94, 0.7)"
                }, {
                    label: "Otras",
                    data: <?= json_encode(array_map(fn($c) => (int)$c["total_obras"] - (int)$c["obras_en_curso"] - (int)$c["obras_finalizadas"], $ranking)) ?>,
                    backgroundColor: "rgba(148, 163, 184, 0.7)"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    });
</script>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";