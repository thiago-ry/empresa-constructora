<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$personal = $reporte->asignacionesPersonal();
$herramientas = $reporte->asignacionesHerramientas();
$materiales = $reporte->asignacionesMateriales();

$personalActivo = count(array_filter($personal, fn($p) => (int)$p["estado"] === 1));
$unidadesEnObra = array_sum(array_column($herramientas, "pendiente"));
$valorEnObra = array_sum(array_map(fn($h) => $h["pendiente"] * (float)$h["costo"], $herramientas));

// Obras que aparecen en alguna asignación, para el filtro
$obras = array_unique(array_merge(
    array_column($personal, "nombre_obra"),
    array_column($herramientas, "nombre_obra"),
    array_column($materiales, "nombre_obra")
));
sort($obras);

// Resumen por obra
$resumen = [];
foreach ($obras as $obra) {
    $resumen[$obra] = ["personal" => 0, "herramientas" => 0, "entregas" => 0];
}
foreach ($personal as $p) {
    if ((int)$p["estado"] === 1) {
        $resumen[$p["nombre_obra"]]["personal"]++;
    }
}
foreach ($herramientas as $h) {
    $resumen[$h["nombre_obra"]]["herramientas"] += $h["pendiente"];
}
foreach ($materiales as $m) {
    $resumen[$m["nombre_obra"]]["entregas"]++;
}

function selectorObras($tabla, $obras)
{
    ?>
    <select class="filter" data-filtro="<?= e($tabla) ?>" data-campo="obra">
        <option value="">Todas las obras</option>
        <?php foreach ($obras as $obra) { ?>
            <option value="<?= e($obra) ?>"><?= e($obra) ?></option>
        <?php } ?>
    </select>
    <?php
}

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Asignaciones</h1>
            <p>Distribución de empleados, herramientas y materiales en cada obra.</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Obras con asignaciones</p>
            <h3><?= count($obras) ?></h3>
            <small>Con personal, herramientas o materiales</small>
        </div>
        <div class="alert alert-info">
            <p>Personal asignado</p>
            <h3><?= $personalActivo ?></h3>
            <small><?= count($personal) - $personalActivo ?> asignaciones finalizadas</small>
        </div>
        <div class="alert alert-warning">
            <p>Herramientas en obra</p>
            <h3><?= $unidadesEnObra ?> unidades</h3>
            <small>Valor: <?= e(Reporte::moneda($valorEnObra)) ?></small>
        </div>
        <div class="alert alert-success">
            <p>Entregas de materiales</p>
            <h3><?= count($materiales) ?></h3>
            <small>Ítems entregados por depósito</small>
        </div>
    </div>

    <div class="table-container reporte-seccion">
        <?php encabezadoImpresion("Reporte de Asignaciones"); ?>
        <div class="table-header">
            <div>
                <h2><i class="fa-solid fa-building"></i> Resumen por obra</h2>
                <small>Personal activo, unidades de herramientas que siguen en la obra y entregas de material recibidas.</small>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th class="numero">Personal activo</th>
                    <th class="numero">Herramientas en obra</th>
                    <th class="numero">Entregas de material</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($resumen) === 0) { ?>
                    <tr><td colspan="4">No hay asignaciones registradas.</td></tr>
                <?php } ?>
                <?php foreach ($resumen as $obra => $r) { ?>
                    <tr>
                        <td><strong><?= e($obra) ?></strong></td>
                        <td class="numero"><?= $r["personal"] ?></td>
                        <td class="numero"><?= $r["herramientas"] ?></td>
                        <td class="numero"><?= $r["entregas"] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="pestanas no-print" data-pestanas>
        <button type="button" class="activa" data-pestana="panelPersonal">
            <i class="fa-solid fa-helmet-safety"></i> Personal (<?= count($personal) ?>)
        </button>
        <button type="button" data-pestana="panelHerramientas">
            <i class="fa-solid fa-screwdriver-wrench"></i> Herramientas (<?= count($herramientas) ?>)
        </button>
        <button type="button" data-pestana="panelMateriales">
            <i class="fa-solid fa-boxes-stacked"></i> Materiales (<?= count($materiales) ?>)
        </button>
    </div>

    <!-- PERSONAL -->
    <div class="table-container reporte-seccion panel-pestana" id="panelPersonal">
        <div class="table-header">
            <div>
                <h2>Personal asignado a obras</h2>
                <small>Mostrando <span data-contador="tablaPersonal"><?= count($personal) ?></span> de <?= count($personal) ?> asignaciones.</small>
            </div>
        </div>
        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaPersonal" placeholder="Buscar empleado o cargo...">
                <?php selectorObras("tablaPersonal", $obras); ?>
                <select class="filter" data-filtro="tablaPersonal" data-campo="estado" data-exportar-estado>
                    <option value="">Activos y retirados</option>
                    <option value="Activo">Activos</option>
                    <option value="Retirado">Retirados</option>
                </select>
            </div>
            <?php botonesReporte("asignaciones_personal", "tablaPersonal"); ?>
        </div>
        <table class="table" id="tablaPersonal">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Empleado</th>
                    <th>Cargo</th>
                    <th>Ingreso</th>
                    <th>Egreso</th>
                    <th>Motivo de egreso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($personal) === 0) { ?>
                    <tr class="fila-vacia"><td colspan="7">No hay personal asignado.</td></tr>
                <?php } ?>
                <?php foreach ($personal as $p) {
                    $estado = (int)$p["estado"] === 1 ? "Activo" : "Retirado"; ?>
                    <tr data-obra="<?= e($p["nombre_obra"]) ?>" data-estado="<?= $estado ?>">
                        <td><?= e($p["nombre_obra"]) ?></td>
                        <td>
                            <strong><?= e($p["empleado"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($p["documento"] ?: "-") ?></span>
                        </td>
                        <td><?= e($p["nombre_cargo"]) ?></td>
                        <td><?= e(Reporte::fecha($p["fecha_ingreso"])) ?></td>
                        <td><?= e(Reporte::fecha($p["fecha_egreso"])) ?></td>
                        <td><?= e($p["motivo_egreso"] ?: "-") ?></td>
                        <td><?= badge($estado) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- HERRAMIENTAS -->
    <div class="table-container reporte-seccion panel-pestana" id="panelHerramientas" hidden>
        <div class="table-header">
            <div>
                <h2>Herramientas asignadas a obras</h2>
                <small>Mostrando <span data-contador="tablaHerramientasObra"><?= count($herramientas) ?></span> de <?= count($herramientas) ?> asignaciones.</small>
            </div>
        </div>
        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaHerramientasObra" placeholder="Buscar herramienta...">
                <?php selectorObras("tablaHerramientasObra", $obras); ?>
                <select class="filter" data-filtro="tablaHerramientasObra" data-campo="estado" data-exportar-estado>
                    <option value="">En obra y devueltas</option>
                    <option value="En obra">En obra</option>
                    <option value="Devuelta">Devueltas</option>
                </select>
            </div>
            <?php botonesReporte("asignaciones_herramientas", "tablaHerramientasObra"); ?>
        </div>
        <table class="table" id="tablaHerramientasObra">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Herramienta</th>
                    <th class="numero">Asignadas</th>
                    <th class="numero">Devueltas</th>
                    <th class="numero">Pendientes</th>
                    <th>Asignación</th>
                    <th>Devolución</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($herramientas) === 0) { ?>
                    <tr class="fila-vacia"><td colspan="8">No hay herramientas asignadas.</td></tr>
                <?php } ?>
                <?php foreach ($herramientas as $h) {
                    $estado = $h["devuelta_total"] ? "Devuelta" : "En obra"; ?>
                    <tr data-obra="<?= e($h["nombre_obra"]) ?>" data-estado="<?= $estado ?>">
                        <td><?= e($h["nombre_obra"]) ?></td>
                        <td>
                            <strong><?= e($h["herramienta"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($h["marca"] ?: "Sin marca") ?></span>
                        </td>
                        <td class="numero"><?= (int)$h["cantidad_asignada"] ?></td>
                        <td class="numero"><?= (int)$h["cantidad_devuelta"] ?></td>
                        <td class="numero"><strong><?= (int)$h["pendiente"] ?></strong></td>
                        <td><?= e(Reporte::fecha($h["fecha_asignacion"])) ?></td>
                        <td><?= e(Reporte::fecha($h["fecha_devolucion"])) ?></td>
                        <td><?= badge($estado) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- MATERIALES -->
    <div class="table-container reporte-seccion panel-pestana" id="panelMateriales" hidden>
        <div class="table-header">
            <div>
                <h2>Materiales entregados a obras</h2>
                <small>Mostrando <span data-contador="tablaMaterialesObra"><?= count($materiales) ?></span> de <?= count($materiales) ?> ítems entregados.</small>
            </div>
        </div>
        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaMaterialesObra" placeholder="Buscar material o quién entregó...">
                <?php selectorObras("tablaMaterialesObra", $obras); ?>
            </div>
            <?php botonesReporte("asignaciones_materiales", "tablaMaterialesObra"); ?>
        </div>
        <table class="table" id="tablaMaterialesObra">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Obra</th>
                    <th>Solicitud</th>
                    <th>Material</th>
                    <th class="numero">Cantidad</th>
                    <th>Entregó</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($materiales) === 0) { ?>
                    <tr class="fila-vacia"><td colspan="6">No hay entregas de materiales.</td></tr>
                <?php } ?>
                <?php foreach ($materiales as $m) { ?>
                    <tr data-obra="<?= e($m["nombre_obra"]) ?>">
                        <td><?= e(Reporte::fecha($m["fecha"], true)) ?></td>
                        <td><?= e($m["nombre_obra"]) ?></td>
                        <td>#<?= (int)$m["id_solicitud"] ?></td>
                        <td><?= e($m["nombre_material"]) ?></td>
                        <td class="numero"><?= e(Reporte::numero($m["cantidad_entregada"])) ?> <?= e($m["unidad_medida"]) ?></td>
                        <td><?= e($m["entrego"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</main>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";