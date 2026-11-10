<?php

require_once __DIR__ . "/../../config/permisos.php";
require_once __DIR__ . "/partes/reporte_comun.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$usuarios = $reporte->usuarios();

$totalUsuarios = count($usuarios);
$activos = 0;
$sinAcceso = 0;
$totalAccesos = 0;
$porRol = [];

foreach ($usuarios as $u) {
    if ((int)$u["estado"] === 1) {
        $activos++;
    }
    if ((int)$u["cantidad_accesos"] === 0) {
        $sinAcceso++;
    }
    $totalAccesos += (int)$u["cantidad_accesos"];
    $porRol[$u["nombre_rol"]] = ($porRol[$u["nombre_rol"]] ?? 0) + 1;
}
ksort($porRol);

// Usuario que más ingresó al sistema
$masActivo = null;
foreach ($usuarios as $u) {
    if ($masActivo === null || (int)$u["cantidad_accesos"] > (int)$masActivo["cantidad_accesos"]) {
        $masActivo = $u;
    }
}

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>
<link rel="stylesheet" href="/empresa_constructora/assets/css/reportes.css">

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Usuarios</h1>
            <p>Usuarios del sistema, roles, estado y actividad (ingresos y acciones registradas).</p>
        </div>
    </div>

    <div class="alert-container cuatro no-print">
        <div class="alert alert-primary">
            <p>Usuarios</p>
            <h3><span data-contador="tablaUsuarios"><?= $totalUsuarios ?></span> de <?= $totalUsuarios ?></h3>
            <small><?= count($porRol) ?> roles</small>
        </div>
        <div class="alert alert-success">
            <p>Activos</p>
            <h3><?= $activos ?></h3>
            <small><?= $totalUsuarios - $activos ?> dados de baja</small>
        </div>
        <div class="alert alert-info">
            <p>Ingresos al sistema</p>
            <h3><?= $totalAccesos ?></h3>
            <small>
                <?php if ($masActivo && (int)$masActivo["cantidad_accesos"] > 0) { ?>
                    Más activo: <?= e($masActivo["usuario"]) ?> (<?= (int)$masActivo["cantidad_accesos"] ?>)
                <?php } else { ?>
                    Sin ingresos registrados
                <?php } ?>
            </small>
        </div>
        <div class="alert alert-warning">
            <p>Nunca ingresaron</p>
            <h3><?= $sinAcceso ?></h3>
            <small>Usuarios sin ningún inicio de sesión</small>
        </div>
    </div>

    <div class="table-container">

        <?php encabezadoImpresion("Reporte de Usuarios"); ?>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" class="search-box" data-buscar="tablaUsuarios" placeholder="Buscar usuario, correo o documento...">
                <select class="filter" data-filtro="tablaUsuarios" data-campo="rol">
                    <option value="">Todos los roles</option>
                    <?php foreach ($porRol as $rol => $cantidad) { ?>
                        <option value="<?= e($rol) ?>"><?= e($rol) ?> (<?= $cantidad ?>)</option>
                    <?php } ?>
                </select>
                <select class="filter" data-filtro="tablaUsuarios" data-campo="estado" data-exportar-estado>
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                </select>
            </div>
            <?php botonesReporte("usuarios", "tablaUsuarios"); ?>
        </div>

        <table class="table" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Documento</th>
                    <th>Rol</th>
                    <th>Alta</th>
                    <th class="numero">Ingresos</th>
                    <th>Último ingreso</th>
                    <th class="numero">Acciones</th>
                    <th>Última acción</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalUsuarios === 0) { ?>
                    <tr class="fila-vacia"><td colspan="9">No hay usuarios registrados.</td></tr>
                <?php } ?>

                <?php foreach ($usuarios as $u) {
                    $estado = (int)$u["estado"] === 1 ? "Activo" : "Inactivo"; ?>
                    <tr data-estado="<?= $estado ?>" data-rol="<?= e($u["nombre_rol"]) ?>">
                        <td>
                            <strong><?= e($u["usuario"]) ?></strong><br>
                            <span class="texto-secundario"><?= e($u["correo"]) ?></span>
                        </td>
                        <td><?= e($u["documento"] ?: "-") ?></td>
                        <td><span class="badge badge-primary"><?= e($u["nombre_rol"]) ?></span></td>
                        <td><?= e(Reporte::fecha($u["fecha_registro"])) ?></td>
                        <td class="numero"><?= (int)$u["cantidad_accesos"] ?></td>
                        <td><?= e(Reporte::fecha($u["ultimo_acceso"], true)) ?></td>
                        <td class="numero"><?= (int)$u["cantidad_acciones"] ?></td>
                        <td><?= e(Reporte::fecha($u["ultima_accion"], true)) ?></td>
                        <td><?= badge($estado) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalUsuarios > 0) { ?>
        <div class="card reporte-seccion no-print">
            <div class="card-header">
                <div>
                    <h2>Usuarios por rol</h2>
                    <p>Cantidad de usuarios registrados en cada rol.</p>
                </div>
            </div>
            <div class="grafico-caja"><canvas id="graficoRoles"></canvas></div>
        </div>
    <?php } ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lienzo = document.getElementById("graficoRoles");
        if (!lienzo || typeof Chart === "undefined") return;

        new Chart(lienzo, {
            type: "bar",
            data: {
                labels: <?= json_encode(array_keys($porRol), JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: "Usuarios",
                    data: <?= json_encode(array_values($porRol)) ?>,
                    backgroundColor: "rgba(245, 180, 0, 0.7)"
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    });
</script>

<?php
$script = "reportes";
require_once __DIR__ . "/../../layouts/footer.php";