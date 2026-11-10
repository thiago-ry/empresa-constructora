<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

// Rango de fechas (por defecto, los últimos 3 meses)
$valida = fn($f) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $f) ? $f : "";
$desde = $valida($_GET["desde"] ?? "") ?: date("Y-m-d", strtotime("-3 months"));
$hasta = $valida($_GET["hasta"] ?? "") ?: date("Y-m-d");

$reporte = new Reporte();
$registros = $reporte->asistencia($desde, $hasta);

$total = count($registros);
$conteo = ["Presente" => 0, "Tarde" => 0, "Ausente" => 0];
$horas = 0;
$porEmpleado = [];
$porDia = [];

foreach ($registros as $r) {
    $conteo[$r["estado"]] = ($conteo[$r["estado"]] ?? 0) + 1;
    $horas += (float)($r["horas"] ?? 0);

    $nombre = $r["empleado"];
    if (!isset($porEmpleado[$nombre])) {
        $porEmpleado[$nombre] = ["Presente" => 0, "Tarde" => 0, "Ausente" => 0, "horas" => 0];
    }
    $porEmpleado[$nombre][$r["estado"]] = ($porEmpleado[$nombre][$r["estado"]] ?? 0) + 1;
    $porEmpleado[$nombre]["horas"] += (float)($r["horas"] ?? 0);

    $dia = $r["fecha"];
    if (!isset($porDia[$dia])) {
        $porDia[$dia] = ["Presente" => 0, "Tarde" => 0, "Ausente" => 0];
    }
    $porDia[$dia][$r["estado"]] = ($porDia[$dia][$r["estado"]] ?? 0) + 1;
}
ksort($porEmpleado);
ksort($porDia);

$porcentajeAsistencia = $total > 0 ? round(($conteo["Presente"] + $conteo["Tarde"]) / $total * 100) : 0;
$obras = array_unique(array_filter(array_column($registros, "nombre_obra")));
sort($obras);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Asistencia</h1>
            <p>Presentes, llegadas tarde, ausencias y horas trabajadas del personal de obra.</p>
        </div>
    </div>

    <form method="get" class="toolbar no-print" style="margin-top:20px;">
        <div class="toolbar-left">
            <label>Desde <input type="date" name="desde" class="filter" value="<?= e($desde) ?>"></label>
            <label>Hasta <input type="date" name="hasta" class="filter" value="<?= e($hasta) ?>"></label>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Aplicar</button>
        </div>
    </form>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Registros del período</p>
            <h3><span data-contador="tablaAsistencia"><?= $total ?></span> de <?= $total ?></h3>
            <small><?= e(Reporte::fecha($desde)) ?> al <?= e(Reporte::fecha($hasta)) ?></small>
        </div>
        <div class="alert alert-success">
            <p>Asistencia</p>
            <h3><?= $porcentajeAsistencia ?>%</h3>
            <small><?= $conteo["Presente"] ?> presentes · <?= $conteo["Tarde"] ?> tardes</small>
        </div>
        <div class="alert alert-danger">
            <p>Ausencias</p>
            <h3><?= $conteo["Ausente"] ?></h3>
            <small><?= count($porEmpleado) ?> empleados con registros</small>
        </div>
        <div class="alert alert-warning">
            <p>Horas trabajadas</p>
            <h3><?= e(Reporte::numero(round($horas, 1))) ?> h</h3>
            <small>Registros con entrada y salida</small>
        </div>
    </div>

    <div class="table-container reporte-seccion">
        <?php encabezadoImpresion("Reporte de Asistencia · " . Reporte::fecha($desde) . " al " . Reporte::fecha($hasta)); ?>
        <div class="table-header">
            <div>
                <h2><i class="fa-solid fa-user-clock"></i> Resumen por empleado</h2>
                <small>Totales del período elegido.</small>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th class="numero">Presentes</th>
                    <th class="numero">Tardes</th>
                    <th class="numero">Ausentes</th>
                    <th>Asistencia</th>
                    <th class="numero">Horas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($porEmpleado) === 0) { ?>
                    <tr><td colspan="6">No hay registros de asistencia en el período.</td></tr>
                <?php } ?>
                <?php foreach ($porEmpleado as $nombre => $p) {
                    $dias = $p["Presente"] + $p["Tarde"] + $p["Ausente"];
                    $pct = $dias > 0 ? round(($p["Presente"] + $p["Tarde"]) / $dias * 100) : 0; ?>
                    <tr>
                        <td><strong><?= e($nombre) ?></strong></td>
                        <td class="numero"><?= $p["Presente"] ?></td>
                        <td class="numero"><?= $p["Tarde"] ?></td>
                        <td class="numero"><?= $p["Ausente"] ?></td>
                        <td style="min-width:140px;"><?= barraAvance($pct) ?></td>
                        <td class="numero"><?= e(Reporte::numero(round($p["horas"], 1))) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="table-container reporte-seccion">
        <div class="table-header">
            <div>
                <h2><i class="fa-solid fa-list"></i> Detalle de registros</h2>
                <small>Cada entrada, salida o ausencia registrada.</small>
            </div>
        </div>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaAsistencia" placeholder="Buscar empleado u observación...">
                <select class="filter" data-filtro="tablaAsistencia" data-campo="obra">
                    <option value="">Todas las obras</option>
                    <?php foreach ($obras as $obra) { ?>
                        <option value="<?= e($obra) ?>"><?= e($obra) ?></option>
                    <?php } ?>
                </select>
                <select class="filter" data-filtro="tablaAsistencia" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <option value="Presente">Presentes</option>
                    <option value="Tarde">Tardes</option>
                    <option value="Ausente">Ausentes</option>
                </select>
            </div>
            <?php botonesReporte("asistencia", "tablaAsistencia", ["desde" => $desde, "hasta" => $hasta]); ?>
        </div>

        <table class="table" id="tablaAsistencia">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Empleado</th>
                    <th>Obra</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th class="numero">Horas</th>
                    <th>Observación</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0) { ?>
                    <tr class="fila-vacia"><td colspan="8">No hay registros de asistencia en el período.</td></tr>
                <?php } ?>
                <?php foreach ($registros as $r) { ?>
                    <tr data-estado="<?= e($r["estado"]) ?>" data-obra="<?= e($r["nombre_obra"] ?? "") ?>">
                        <td><?= e(Reporte::fecha($r["fecha"])) ?></td>
                        <td>
                            <strong><?= e($r["empleado"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($r["documento"] ?: "-") ?></span>
                        </td>
                        <td><?= e($r["nombre_obra"] ?: "-") ?></td>
                        <td><?= $r["hora_entrada"] ? e(substr($r["hora_entrada"], 0, 5)) : "-" ?></td>
                        <td><?= $r["hora_salida"] ? e(substr($r["hora_salida"], 0, 5)) : "-" ?></td>
                        <td class="numero"><?= $r["horas"] === null ? "-" : e(Reporte::numero($r["horas"])) ?></td>
                        <td><?= e($r["observacion"] ?: "-") ?></td>
                        <td><?= badge($r["estado"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if (count($porDia) > 0) { ?>
        <div class="card reporte-seccion no-print">
            <div class="card-header">
                <div>
                    <h2>Asistencia por día</h2>
                    <p>Presentes, tardes y ausentes de cada día del período.</p>
                </div>
            </div>
            <div class="grafico-caja"><canvas id="graficoDias"></canvas></div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lienzo = document.getElementById("graficoDias");
        if (!lienzo || typeof Chart === "undefined") return;

        new Chart(lienzo, {
            type: "bar",
            data: {
                labels: <?= json_encode(array_map(fn($d) => Reporte::fecha($d), array_keys($porDia))) ?>,
                datasets: [{
                    label: "Presente",
                    data: <?= json_encode(array_column(array_values($porDia), "Presente")) ?>,
                    backgroundColor: "#22c55e"
                }, {
                    label: "Tarde",
                    data: <?= json_encode(array_column(array_values($porDia), "Tarde")) ?>,
                    backgroundColor: "#f5b400"
                }, {
                    label: "Ausente",
                    data: <?= json_encode(array_column(array_values($porDia), "Ausente")) ?>,
                    backgroundColor: "#ef4444"
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