<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$incidencias = $reporte->incidencias();

$total = count($incidencias);
$porEstado = ["Pendiente" => 0, "En revisión" => 0, "Resuelta" => 0];
$porTipo = [];
$porObra = [];

foreach ($incidencias as $i) {
    $porEstado[$i["estado"]] = ($porEstado[$i["estado"]] ?? 0) + 1;
    $porTipo[$i["tipo_incidencia"]] = ($porTipo[$i["tipo_incidencia"]] ?? 0) + 1;
    $porObra[$i["nombre_obra"]] = ($porObra[$i["nombre_obra"]] ?? 0) + 1;
}
arsort($porTipo);
arsort($porObra);

$abiertas = $total - $porEstado["Resuelta"];
$tipos = array_keys($porTipo);
sort($tipos);
$obras = array_keys($porObra);
sort($obras);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Incidencias</h1>
            <p>Problemas registrados en las obras, su tipo, estado y la solución aplicada.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Incidencias</p>
            <h3><span data-contador="tablaIncidencias"><?= $total ?></span> de <?= $total ?></h3>
            <small><?= count($porObra) ?> obras con incidencias</small>
        </div>
        <div class="alert alert-danger">
            <p>Abiertas</p>
            <h3><?= $abiertas ?></h3>
            <small><?= $porEstado["Pendiente"] ?> pendientes · <?= $porEstado["En revisión"] ?> en revisión</small>
        </div>
        <div class="alert alert-success">
            <p>Resueltas</p>
            <h3><?= $porEstado["Resuelta"] ?></h3>
            <small><?= $total > 0 ? round($porEstado["Resuelta"] / $total * 100) : 0 ?>% del total</small>
        </div>
        <div class="alert alert-warning">
            <p>Tipo más frecuente</p>
            <h3><?= $total > 0 ? e(array_key_first($porTipo)) : "-" ?></h3>
            <small><?= $total > 0 ? reset($porTipo) . " incidencias" : "Sin datos" ?></small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte de Incidencias"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaIncidencias" placeholder="Buscar en la descripción o la solución...">
                <select class="filter" data-filtro="tablaIncidencias" data-campo="obra">
                    <option value="">Todas las obras</option>
                    <?php foreach ($obras as $obra) { ?>
                        <option value="<?= e($obra) ?>"><?= e($obra) ?></option>
                    <?php } ?>
                </select>
                <select class="filter" data-filtro="tablaIncidencias" data-campo="tipo">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $tipo) { ?>
                        <option value="<?= e($tipo) ?>"><?= e($tipo) ?></option>
                    <?php } ?>
                </select>
                <select class="filter" data-filtro="tablaIncidencias" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendientes</option>
                    <option value="En revisión">En revisión</option>
                    <option value="Resuelta">Resueltas</option>
                </select>
            </div>
            <?php botonesReporte("incidencias", "tablaIncidencias"); ?>
        </div>

        <table class="table" id="tablaIncidencias">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Obra</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Solución</th>
                    <th class="numero">Fotos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0) { ?>
                    <tr class="fila-vacia"><td colspan="7">No hay incidencias registradas.</td></tr>
                <?php } ?>
                <?php foreach ($incidencias as $i) { ?>
                    <tr data-estado="<?= e($i["estado"]) ?>" data-tipo="<?= e($i["tipo_incidencia"]) ?>" data-obra="<?= e($i["nombre_obra"]) ?>">
                        <td><?= e(Reporte::fecha($i["fecha"])) ?></td>
                        <td><?= e($i["nombre_obra"]) ?></td>
                        <td><?= e($i["tipo_incidencia"]) ?></td>
                        <td><?= nl2br(e($i["descripcion"] ?: "-")) ?></td>
                        <td><?= nl2br(e($i["solucion"] ?: "-")) ?></td>
                        <td class="numero"><?= (int)$i["fotos"] ?></td>
                        <td><?= badge($i["estado"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($total > 0) { ?>
        <div class="graficos-reporte no-print">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Incidencias por tipo</h2>
                        <p>Qué problemas se repiten más.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoTipos"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Incidencias por obra</h2>
                        <p>Cantidad de incidencias registradas en cada obra.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoObras"></canvas></div>
            </div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Chart === "undefined") return;

        const tipos = document.getElementById("graficoTipos");
        if (tipos) {
            new Chart(tipos, {
                type: "doughnut",
                data: {
                    labels: <?= json_encode(array_keys($porTipo), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{ data: <?= json_encode(array_values($porTipo)) ?> }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        const obras = document.getElementById("graficoObras");
        if (obras) {
            new Chart(obras, {
                type: "bar",
                data: {
                    labels: <?= json_encode(array_keys($porObra), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: "Incidencias",
                        data: <?= json_encode(array_values($porObra)) ?>,
                        backgroundColor: "rgba(239, 68, 68, 0.7)"
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