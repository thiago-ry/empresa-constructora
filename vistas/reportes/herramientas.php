<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$herramientas = $reporte->herramientas();

$totalHerramientas = count($herramientas);
$unidades = 0;
$disponibles = 0;
$asignadas = 0;
$reparacion = 0;
$fuera = 0;
$valorTotal = 0;
$solicitadas = 0;
$porTipo = [];

foreach ($herramientas as $h) {
    $unidades += (int)$h["unidades"];
    $disponibles += (int)$h["disponibles"];
    $asignadas += (int)$h["asignadas"];
    $reparacion += (int)$h["en_reparacion"];
    $fuera += (int)$h["fuera_servicio"];
    $valorTotal += $h["valor_total"];
    $solicitadas += (int)$h["solicitadas_pendientes"];
    $tipo = $h["tipo"] ?: "Sin tipo";
    $porTipo[$tipo] = ($porTipo[$tipo] ?? 0) + (int)$h["unidades"];
}
ksort($porTipo);

$tipos = array_keys($porTipo);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Herramientas</h1>
            <p>Inventario por unidad, estado, obras donde están asignadas y valor del equipamiento.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Herramientas</p>
            <h3><span data-contador="tablaHerramientas"><?= $totalHerramientas ?></span> de <?= $totalHerramientas ?></h3>
            <small><?= $unidades ?> unidades en total</small>
        </div>
        <div class="alert alert-success">
            <p>Disponibles</p>
            <h3><?= $disponibles ?></h3>
            <small><?= $unidades > 0 ? round($disponibles / $unidades * 100) : 0 ?>% de las unidades</small>
        </div>
        <div class="alert alert-info">
            <p>Asignadas a obras</p>
            <h3><?= $asignadas ?></h3>
            <small><?= $reparacion ?> en reparación · <?= $fuera ?> fuera de servicio</small>
        </div>
        <div class="alert alert-warning">
            <p>Valor del equipamiento</p>
            <h3><?= e(Reporte::moneda($valorTotal)) ?></h3>
            <small><?= $solicitadas ?> unidades pedidas en solicitudes pendientes</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte de Herramientas"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaHerramientas" placeholder="Buscar herramienta, marca u obra...">
                <select class="filter" data-filtro="tablaHerramientas" data-campo="tipo">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $tipo) { ?>
                        <option value="<?= e($tipo) ?>"><?= e($tipo) ?></option>
                    <?php } ?>
                </select>
                <select class="filter" data-filtro="tablaHerramientas" data-campo="estado" data-exportar-estado>
                    <option value="">Con y sin asignaciones</option>
                    <option value="Con asignaciones">Con unidades en obra</option>
                    <option value="Sin asignaciones">Todas en depósito</option>
                </select>
            </div>
            <?php botonesReporte("herramientas", "tablaHerramientas"); ?>
        </div>

        <table class="table" id="tablaHerramientas">
            <thead>
                <tr>
                    <th>Herramienta</th>
                    <th>Tipo</th>
                    <th class="numero">Unidades</th>
                    <th class="numero">Disponibles</th>
                    <th class="numero">Asignadas</th>
                    <th class="numero">Reparación / Fuera</th>
                    <th>Uso</th>
                    <th>Obras actuales</th>
                    <th class="numero">Costo unitario</th>
                    <th class="numero">Valor total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalHerramientas === 0) { ?>
                    <tr class="fila-vacia"><td colspan="10">No hay herramientas registradas.</td></tr>
                <?php } ?>

                <?php foreach ($herramientas as $h) { ?>
                    <tr data-tipo="<?= e($h["tipo"] ?: "Sin tipo") ?>"
                        data-estado="<?= (int)$h["asignadas"] > 0 ? "Con asignaciones" : "Sin asignaciones" ?>">
                        <td>
                            <strong><?= e($h["nombre"]) ?></strong><br>
                            <span class="texto-secundario">
                                <?= e(trim(($h["marca"] ?? "") . " " . ($h["modelo"] ?? "")) ?: "Sin marca") ?>
                                · Compra: <?= e(Reporte::fecha($h["fecha_adquisicion"])) ?>
                            </span>
                        </td>
                        <td><?= e($h["tipo"] ?: "-") ?></td>
                        <td class="numero"><?= (int)$h["unidades"] ?></td>
                        <td class="numero"><?= (int)$h["disponibles"] ?></td>
                        <td class="numero"><?= (int)$h["asignadas"] ?></td>
                        <td class="numero"><?= (int)$h["en_reparacion"] ?> / <?= (int)$h["fuera_servicio"] ?></td>
                        <td style="min-width:120px;"><?= barraAvance($h["uso_pct"]) ?></td>
                        <td>
                            <?= e($h["obras"] ?: "-") ?>
                            <?php if ((int)$h["solicitadas_pendientes"] > 0) { ?>
                                <br><span class="badge badge-warning"><?= (int)$h["solicitadas_pendientes"] ?> pedidas</span>
                            <?php } ?>
                        </td>
                        <td class="numero"><?= e(Reporte::moneda($h["costo"])) ?></td>
                        <td class="numero"><?= e(Reporte::moneda($h["valor_total"])) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalHerramientas > 0) { ?>
        <div class="graficos-reporte no-print">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Estado de las unidades</h2>
                        <p>Situación actual de todas las unidades registradas.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoEstado"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Unidades por tipo</h2>
                        <p>Cantidad de unidades según el tipo de herramienta.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoTipo"></canvas></div>
            </div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Chart === "undefined") return;

        const estado = document.getElementById("graficoEstado");
        if (estado) {
            new Chart(estado, {
                type: "doughnut",
                data: {
                    labels: ["Disponibles", "Asignadas", "En reparación", "Fuera de servicio"],
                    datasets: [{
                        data: [<?= $disponibles ?>, <?= $asignadas ?>, <?= $reparacion ?>, <?= $fuera ?>],
                        backgroundColor: ["#22c55e", "#3b82f6", "#f5b400", "#ef4444"]
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        const tipo = document.getElementById("graficoTipo");
        if (tipo) {
            new Chart(tipo, {
                type: "bar",
                data: {
                    labels: <?= json_encode($tipos, JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: "Unidades",
                        data: <?= json_encode(array_values($porTipo)) ?>,
                        backgroundColor: "rgba(245, 180, 0, 0.7)"
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        }
    });
</script>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";