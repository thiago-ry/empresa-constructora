<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$obras = $reporte->obras();

$totalObras = count($obras);
$porEstado = [];
$avanceTotal = 0;
$presupuestoTotal = 0;
$incidenciasAbiertas = 0;
$obraMayor = null;
$obraMenor = null;

foreach ($obras as $obra) {
    $porEstado[$obra["estado"]] = ($porEstado[$obra["estado"]] ?? 0) + 1;
    $avanceTotal += $obra["avance"];
    $presupuestoTotal += (float)($obra["presupuesto"] ?? 0);
    $incidenciasAbiertas += (int)$obra["incidencias_abiertas"];

    if ($obraMayor === null || $obra["avance"] > $obraMayor["avance"]) {
        $obraMayor = $obra;
    }
    if ($obraMenor === null || $obra["avance"] < $obraMenor["avance"]) {
        $obraMenor = $obra;
    }
}

$enCurso = ($porEstado["Planificacion"] ?? 0) + ($porEstado["En Proceso"] ?? 0);
$finalizadas = $porEstado["Finalizada"] ?? 0;
$promedioAvance = $totalObras > 0 ? round($avanceTotal / $totalObras) : 0;

$estados = ["Planificacion" => "Planificación", "En Proceso" => "En proceso", "Finalizada" => "Finalizada",
    "Suspendida" => "Suspendida", "Cancelada" => "Cancelada"];

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte General de Obras</h1>
            <p>Estado, avance, personal, recursos, incidencias y presupuesto de cada obra.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Obras registradas</p>
            <h3><span data-contador="tablaObras"><?= $totalObras ?></span> de <?= $totalObras ?></h3>
            <small><?= $enCurso ?> en curso · <?= $finalizadas ?> finalizadas</small>
        </div>
        <div class="alert alert-warning">
            <p>Avance promedio</p>
            <h3><?= $promedioAvance ?>%</h3>
            <small>Según las etapas finalizadas</small>
        </div>
        <div class="alert alert-success">
            <p>Presupuesto total</p>
            <h3><?= e(Reporte::moneda($presupuestoTotal)) ?></h3>
            <small>Última versión de cada obra</small>
        </div>
        <div class="alert alert-danger">
            <p>Incidencias abiertas</p>
            <h3><?= $incidenciasAbiertas ?></h3>
            <small>Pendientes o en revisión</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte General de Obras"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaObras"
                    placeholder="Buscar por obra, cliente, responsable o dirección...">
                <select class="filter" data-filtro="tablaObras" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <?php foreach ($estados as $valor => $texto) { ?>
                        <option value="<?= e($valor) ?>"><?= e($texto) ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php botonesReporte("obras", "tablaObras"); ?>
        </div>

        <table class="table" id="tablaObras">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Cliente</th>
                    <th>Responsables</th>
                    <th>Fechas</th>
                    <th>Avance</th>
                    <th>Recursos</th>
                    <th>Incidencias</th>
                    <th>Presupuesto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalObras === 0) { ?>
                    <tr class="fila-vacia"><td colspan="9">No hay obras registradas.</td></tr>
                <?php } ?>

                <?php foreach ($obras as $obra) { ?>
                    <tr data-estado="<?= e($obra["estado"]) ?>">
                        <td>
                            <strong><?= e($obra["nombre_obra"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($obra["direccion"]) ?></span>
                        </td>
                        <td>
                            <?= e($obra["cliente"]) ?>
                            <?php if (!empty($obra["cliente_telefono"])) { ?>
                                <br><span class="texto-secundario"><?= e($obra["cliente_telefono"]) ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <span class="texto-secundario">Jefe:</span> <?= e($obra["jefe_obra"]) ?><br>
                            <span class="texto-secundario">Capataz:</span> <?= e($obra["capataz"]) ?>
                        </td>
                        <td>
                            <span class="texto-secundario">Inicio:</span> <?= e(Reporte::fecha($obra["fecha_inicio"])) ?><br>
                            <span class="texto-secundario">Fin:</span> <?= e(Reporte::fecha($obra["fecha_fin"])) ?>
                        </td>
                        <td style="min-width:170px;">
                            <?= barraAvance($obra["avance"]) ?>
                            <span class="texto-secundario">
                                <?= (int)$obra["etapas_finalizadas"] ?> de <?= (int)$obra["total_etapas"] ?> etapas
                                · <?= (int)$obra["total_avances"] ?> avances
                            </span>
                        </td>
                        <td>
                            <?= (int)$obra["empleados_activos"] ?> empleados<br>
                            <?= (int)$obra["herramientas_en_obra"] ?> herramientas<br>
                            <span class="texto-secundario">
                                <?= (int)$obra["entregas_material"] ?> entregas de material
                                <?php if ((int)$obra["solicitudes_pendientes"] > 0) { ?>
                                    · <?= (int)$obra["solicitudes_pendientes"] ?> pedidos pendientes
                                <?php } ?>
                            </span>
                        </td>
                        <td>
                            <?= (int)$obra["incidencias_abiertas"] ?> abiertas<br>
                            <span class="texto-secundario"><?= (int)$obra["total_incidencias"] ?> en total</span>
                        </td>
                        <td class="numero">
                            <?php if ($obra["presupuesto"] === null) { ?>
                                <span class="texto-secundario">Sin presupuesto</span>
                            <?php } else { ?>
                                <?= e(Reporte::moneda($obra["presupuesto"])) ?><br>
                                <span class="texto-secundario">
                                    v<?= (int)$obra["versiones_presupuesto"] ?> · <?= e($obra["presupuesto_estado"]) ?>
                                </span>
                            <?php } ?>
                        </td>
                        <td><?= badge($obra["estado"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <?php if ($totalObras > 0) { ?>
        <div class="alert-container no-print" style="grid-template-columns:repeat(2,1fr);">
            <div class="alert alert-success">
                <p>Obra con mayor avance</p>
                <h3><?= e($obraMayor["nombre_obra"]) ?> (<?= (int)$obraMayor["avance"] ?>%)</h3>
            </div>
            <div class="alert alert-warning">
                <p>Obra con menor avance</p>
                <h3><?= e($obraMenor["nombre_obra"]) ?> (<?= (int)$obraMenor["avance"] ?>%)</h3>
            </div>
        </div>

        <div class="graficos-reporte no-print">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Avance de las obras</h2>
                        <p>Porcentaje de etapas finalizadas en cada obra.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoAvance"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Obras por estado</h2>
                        <p>Cantidad de obras en cada estado.</p>
                    </div>
                </div>
                <div class="grafico-caja"><canvas id="graficoEstados"></canvas></div>
            </div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Chart === "undefined") return;

        const avance = document.getElementById("graficoAvance");
        if (avance) {
            new Chart(avance, {
                type: "bar",
                data: {
                    labels: <?= json_encode(array_column($obras, "nombre_obra"), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        label: "Avance (%)",
                        data: <?= json_encode(array_map("intval", array_column($obras, "avance"))) ?>,
                        backgroundColor: "rgba(245, 180, 0, 0.7)"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }

        const estados = document.getElementById("graficoEstados");
        if (estados) {
            new Chart(estados, {
                type: "doughnut",
                data: {
                    labels: <?= json_encode(array_map(fn($k) => $estados[$k] ?? $k, array_keys($porEstado)), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{ data: <?= json_encode(array_values($porEstado)) ?> }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    });
</script>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";