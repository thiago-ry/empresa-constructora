<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$empleados = $reporte->empleados();

$totalEmpleados = count($empleados);
$activos = 0;
$asignados = 0;
$masaSalarial = 0;
$presentes = 0;
$tardes = 0;
$ausentes = 0;
$cargos = [];

foreach ($empleados as $empleado) {
    if ((int)$empleado["estado"] === 1) {
        $activos++;
        $masaSalarial += (float)$empleado["salario"];
    }
    if ((int)$empleado["cantidad_obras"] > 0) {
        $asignados++;
    }
    $presentes += (int)$empleado["presentes"];
    $tardes += (int)$empleado["tardes"];
    $ausentes += (int)$empleado["ausentes"];

    foreach (explode(", ", $empleado["nombre_cargo"]) as $cargo) {
        $cargos[$cargo] = ($cargos[$cargo] ?? 0) + 1;
    }
}
ksort($cargos);

$diasRegistrados = $presentes + $tardes + $ausentes;
$asistenciaGeneral = $diasRegistrados > 0 ? round(($presentes + $tardes) / $diasRegistrados * 100) : 0;

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Empleados</h1>
            <p>Personal, cargos, obras asignadas, asistencia, tareas y salarios.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Empleados</p>
            <h3><span data-contador="tablaEmpleados"><?= $totalEmpleados ?></span> de <?= $totalEmpleados ?></h3>
            <small><?= $activos ?> activos · <?= $totalEmpleados - $activos ?> inactivos</small>
        </div>
        <div class="alert alert-info">
            <p>Asignados a obras</p>
            <h3><?= $asignados ?></h3>
            <small><?= $totalEmpleados - $asignados ?> sin obra actualmente</small>
        </div>
        <div class="alert alert-success">
            <p>Asistencia general</p>
            <h3><?= $asistenciaGeneral ?>%</h3>
            <small><?= $presentes ?> presentes · <?= $tardes ?> tardes · <?= $ausentes ?> ausencias</small>
        </div>
        <div class="alert alert-warning">
            <p>Masa salarial mensual</p>
            <h3><?= e(Reporte::moneda($masaSalarial)) ?></h3>
            <small>Suma de salarios del personal activo</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte General de Empleados"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaEmpleados" placeholder="Buscar empleado, documento u obra...">
                <select class="filter" data-filtro="tablaEmpleados" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                </select>
                <select class="filter" data-filtro="tablaEmpleados" data-campo="asignacion">
                    <option value="">Con y sin obra</option>
                    <option value="asignado">Con obra</option>
                    <option value="sin-obra">Sin obra</option>
                </select>
                <select class="filter" data-filtro="tablaEmpleados" data-campo="cargo" data-modo="lista">
                    <option value="">Todos los cargos</option>
                    <?php foreach ($cargos as $cargo => $cantidad) { ?>
                        <option value="<?= e($cargo) ?>"><?= e($cargo) ?> (<?= $cantidad ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <?php botonesReporte("empleados", "tablaEmpleados"); ?>
        </div>

        <table class="table" id="tablaEmpleados">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Documento</th>
                    <th>Cargo</th>
                    <th>Obras actuales</th>
                    <th>Asistencia</th>
                    <th>Tareas</th>
                    <th>Salario</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalEmpleados === 0) { ?>
                    <tr class="fila-vacia"><td colspan="8">No hay empleados registrados.</td></tr>
                <?php } ?>

                <?php foreach ($empleados as $empleado) {
                    $asignado = (int)$empleado["cantidad_obras"] > 0;
                    $estado = (int)$empleado["estado"] === 1 ? "Activo" : "Inactivo";
                    // Todos los cargos del empleado, para poder filtrar por cualquiera
                    $listaCargos = str_replace(", ", "|", $empleado["nombre_cargo"]);
                ?>
                    <tr data-estado="<?= $estado ?>"
                        data-asignacion="<?= $asignado ? "asignado" : "sin-obra" ?>"
                        data-cargo="<?= e($listaCargos) ?>">
                        <td>
                            <strong><?= e($empleado["empleado"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($empleado["correo"]) ?> · <?= e($empleado["telefono"] ?: "sin teléfono") ?></span>
                        </td>
                        <td><?= e($empleado["documento"] ?: "-") ?></td>
                        <td><?= e($empleado["nombre_cargo"]) ?></td>
                        <td>
                            <?php if ($asignado) { ?>
                                <?= e($empleado["obras"]) ?>
                            <?php } else { ?>
                                <span class="badge badge-secondary">Sin obra</span>
                            <?php } ?>
                            <br><span class="texto-secundario"><?= (int)$empleado["obras_historicas"] ?> obras en total</span>
                        </td>
                        <td>
                            <?php if ($empleado["asistencia_pct"] === null) { ?>
                                <span class="texto-secundario">Sin registros</span>
                            <?php } else { ?>
                                <?= (int)$empleado["asistencia_pct"] ?>%<br>
                                <span class="texto-secundario">
                                    <?= (int)$empleado["presentes"] ?> P · <?= (int)$empleado["tardes"] ?> T · <?= (int)$empleado["ausentes"] ?> A
                                </span>
                            <?php } ?>
                        </td>
                        <td>
                            <?= (int)$empleado["tareas_completadas"] ?> de <?= (int)$empleado["tareas_asignadas"] ?><br>
                            <span class="texto-secundario">completadas</span>
                        </td>
                        <td class="numero">
                            <?= $empleado["salario"] !== null && $empleado["salario"] !== ""
                                ? e(Reporte::moneda($empleado["salario"])) : "-" ?>
                        </td>
                        <td><?= badge($estado) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <?php if ($totalEmpleados > 0) { ?>
        <div class="graficos-reporte no-print">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Empleados por cargo</h2>
                        <p>Un empleado con dos cargos se cuenta en ambos.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoCargos"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Asistencia registrada</h2>
                        <p>Total de días presentes, con llegada tarde y ausentes.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoAsistencia"></canvas></div>
            </div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Chart === "undefined") return;

        const cargos = document.getElementById("graficoCargos");
        if (cargos) {
            new Chart(cargos, {
                type: "bar",
                data: {
                    labels: <?= json_encode(array_keys($cargos), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: "Empleados",
                        data: <?= json_encode(array_values($cargos)) ?>,
                        backgroundColor: "rgba(245, 180, 0, 0.7)"
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        }

        const asistencia = document.getElementById("graficoAsistencia");
        if (asistencia) {
            new Chart(asistencia, {
                type: "doughnut",
                data: {
                    labels: ["Presente", "Tarde", "Ausente"],
                    datasets: [{
                        data: [<?= $presentes ?>, <?= $tardes ?>, <?= $ausentes ?>],
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