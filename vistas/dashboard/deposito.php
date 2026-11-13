
<?php
require_once __DIR__ . "/../../modelos/Conexion.php";
require_once __DIR__ . "/../../modelos/Material.php";
require_once __DIR__ . "/../../modelos/SolicitudMaterial.php";
require_once __DIR__ . "/../../modelos/SolicitudHerramienta.php";

$db = new Conexion();
$conexion = $db->conectar();

$materialModel = new Material();
$solicitudMaterialModel = new SolicitudMaterial();
$solicitudHerramientaModel = new SolicitudHerramienta($conexion);

$materiales = $materialModel->obtenerTodos();
$solicitudesMateriales = $solicitudMaterialModel->obtenerTodas();
$solicitudesHerramientas = $solicitudHerramientaModel->obtenerTodas();

$materiales = is_array($materiales) ? $materiales : [];
$solicitudesMateriales = is_array($solicitudesMateriales) ? $solicitudesMateriales : [];
$solicitudesHerramientas = is_array($solicitudesHerramientas) ? $solicitudesHerramientas : [];

function escaparDeposito($valor)
{
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

function estadoPendienteDeposito($estado)
{
    return strtolower(trim((string)$estado)) === "pendiente";
}

/* Estadísticas de materiales */
$totalMateriales = 0;
$materialesBajoStock = 0;
$unidadesStock = 0;

foreach ($materiales as $material) {
    if (isset($material["estado"]) && (int)$material["estado"] !== 1) {
        continue;
    }

    $totalMateriales++;
    $stock = (float)($material["stock"] ?? 0);
    $minimo = (float)($material["stock_minimo"] ?? 0);

    $unidadesStock += $stock;

    if ($stock <= $minimo) {
        $materialesBajoStock++;
    }
}

/* Solicitudes pendientes */
$pendientesMateriales = 0;
foreach ($solicitudesMateriales as $solicitud) {
    if (estadoPendienteDeposito($solicitud["estado"] ?? "")) {
        $pendientesMateriales++;
    }
}

$pendientesHerramientas = 0;
foreach ($solicitudesHerramientas as $solicitud) {
    if (estadoPendienteDeposito($solicitud["estado"] ?? "")) {
        $pendientesHerramientas++;
    }
}

$totalPendientes = $pendientesMateriales + $pendientesHerramientas;

/* Materiales que requieren reposición */
$materialesCriticos = [];

foreach ($materiales as $material) {
    if (isset($material["estado"]) && (int)$material["estado"] !== 1) {
        continue;
    }

    $stock = (float)($material["stock"] ?? 0);
    $minimo = (float)($material["stock_minimo"] ?? 0);

    if ($stock <= $minimo) {
        $materialesCriticos[] = $material;
    }
}

$materialesCriticos = array_slice($materialesCriticos, 0, 5);
$solicitudesMateriales = array_slice($solicitudesMateriales, 0, 5);
$solicitudesHerramientas = array_slice($solicitudesHerramientas, 0, 5);

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";
?>

<main class="content dashboard-deposito">

    <section class="deposito-header">
        <div>
            <span class="deposito-eyebrow">GESTIÓN DE DEPÓSITO</span>
            <h1>Dashboard</h1>
            <p>Controlá el inventario, supervisá el stock y gestioná las solicitudes de materiales y herramientas.</p>
        </div>

        <div class="deposito-fecha">
            <i class="fa-solid fa-calendar-days"></i>
            <?= date("d/m/Y") ?>
        </div>
    </section>

    <!-- INDICADORES -->
    <section class="deposito-stats">

        <article class="deposito-stat">
            <div class="deposito-stat-icon blue">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span>Materiales registrados</span>
                <h2><?= $totalMateriales ?></h2>
                <small>Materiales activos</small>
            </div>
        </article>

        <article class="deposito-stat">
            <div class="deposito-stat-icon yellow">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span>Stock bajo o crítico</span>
                <h2><?= $materialesBajoStock ?></h2>
                <small>Requieren revisión</small>
            </div>
        </article>

        <article class="deposito-stat">
            <div class="deposito-stat-icon green">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <span>Unidades en stock</span>
                <h2><?= number_format($unidadesStock, 0, ",", ".") ?></h2>
                <small>Existencias registradas</small>
            </div>
        </article>

        <article class="deposito-stat">
            <div class="deposito-stat-icon orange">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <span>Solicitudes pendientes</span>
                <h2><?= $totalPendientes ?></h2>
                <small>Materiales y herramientas</small>
            </div>
        </article>

    </section>

    <!-- ACCESOS RÁPIDOS -->
    <section class="deposito-section">
        <div class="deposito-section-title">
            <div>
                <h2>Accesos rápidos</h2>
                <p>Accedé directamente a las principales tareas del depósito.</p>
            </div>
        </div>

        <div class="deposito-quick-grid">

            <a href="/empresa_constructora/vistas/materiales/" class="quick-card">
                <div class="quick-icon blue">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <strong>Materiales</strong>
                    <span>Consultar y administrar inventario</span>
                </div>
                <i class="fa-solid fa-arrow-right quick-arrow"></i>
            </a>

            <a href="/empresa_constructora/vistas/herramientas/" class="quick-card">
                <div class="quick-icon green">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <strong>Herramientas</strong>
                    <span>Consultar herramientas y unidades</span>
                </div>
                <i class="fa-solid fa-arrow-right quick-arrow"></i>
            </a>

            <a href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar" class="quick-card">
                <div class="quick-icon orange">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <strong>Solicitudes de herramientas</strong>
                    <span>Revisar, aprobar y registrar entregas</span>
                </div>
                <i class="fa-solid fa-arrow-right quick-arrow"></i>
            </a>

            <a href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=gerente" class="quick-card">
                <div class="quick-icon yellow">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <strong>Solicitudes de materiales</strong>
                    <span>Consultar solicitudes de las obras</span>
                </div>
                <i class="fa-solid fa-arrow-right quick-arrow"></i>
            </a>

        </div>
    </section>

    <!-- DOS COLUMNAS -->
    <section class="deposito-columns">

        <!-- STOCK CRÍTICO -->
        <div class="deposito-section">
            <div class="deposito-section-title">
                <div>
                    <h2>Control de stock</h2>
                    <p>Materiales que alcanzaron o están por debajo del mínimo.</p>
                </div>
                <span class="section-symbol warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>

            <?php if (!empty($materialesCriticos)): ?>
                <div class="deposito-list">
                    <?php foreach ($materialesCriticos as $material): ?>
                        <div class="stock-item">
                            <div class="stock-icon">
                                <i class="fa-solid fa-box"></i>
                            </div>

                            <div class="stock-info">
                                <strong><?= escaparDeposito($material["nombre_material"] ?? "Material") ?></strong>
                                <small>
                                    Mínimo: <?= escaparDeposito($material["stock_minimo"] ?? 0) ?>
                                    <?= escaparDeposito($material["unidad_medida"] ?? "") ?>
                                </small>
                            </div>

                            <div class="stock-value">
                                <strong><?= escaparDeposito($material["stock"] ?? 0) ?></strong>
                                <span>Disponible</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="deposito-empty">
                    <i class="fa-solid fa-circle-check"></i>
                    <p>No hay materiales con stock crítico.</p>
                </div>
            <?php endif; ?>

            <a href="/empresa_constructora/vistas/materiales/" class="section-link">
                Ver inventario completo <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- SOLICITUDES DE HERRAMIENTAS -->
        <div class="deposito-section">
            <div class="deposito-section-title">
                <div>
                    <h2>Solicitudes de herramientas</h2>
                    <p>Últimas solicitudes registradas.</p>
                </div>
                <span class="section-symbol">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </span>
            </div>

            <?php if (!empty($solicitudesHerramientas)): ?>
                <div class="deposito-list">
                    <?php foreach ($solicitudesHerramientas as $solicitud): ?>
                        <div class="request-item">
                            <div class="request-info">
                                <strong>
                                    Solicitud #<?= escaparDeposito($solicitud["id_solicitud"] ?? "") ?>
                                </strong>
                                <small>
                                    <?= escaparDeposito($solicitud["nombre_obra"] ?? "Obra") ?>
                                </small>
                            </div>

                            <?php
                            $estado = $solicitud["estado"] ?? "Sin estado";
                            $claseEstado = strtolower($estado);
                            ?>
                            <span class="request-status <?= escaparDeposito($claseEstado) ?>">
                                <?= escaparDeposito($estado) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="deposito-empty">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <p>No hay solicitudes de herramientas.</p>
                </div>
            <?php endif; ?>

            <a href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar" class="section-link">
                Gestionar solicitudes <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </section>

    <!-- SOLICITUDES DE MATERIALES -->
    <section class="deposito-section solicitudes-materiales">
        <div class="deposito-section-title">
            <div>
                <h2>Solicitudes de materiales</h2>
                <p>Solicitudes registradas para las distintas obras.</p>
            </div>
            <span class="section-symbol yellow">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </span>
        </div>

        <?php if (!empty($solicitudesMateriales)): ?>
            <div class="deposito-table-container">
                <table class="deposito-table">
                    <thead>
                        <tr>
                            <th>Solicitud</th>
                            <th>Obra</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudesMateriales as $solicitud): ?>
                            <tr>
                                <td>
                                    <strong>
                                        #<?= escaparDeposito($solicitud["id_solicitud"] ?? "") ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= escaparDeposito($solicitud["nombre_obra"] ?? "Obra") ?>
                                </td>
                                <td>
                                    <?php
                                    $estado = $solicitud["estado"] ?? "Sin estado";
                                    $claseEstado = strtolower($estado);
                                    ?>
                                    <span class="request-status <?= escaparDeposito($claseEstado) ?>">
                                        <?= escaparDeposito($estado) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="deposito-empty">
                <i class="fa-solid fa-inbox"></i>
                <p>No hay solicitudes de materiales registradas.</p>
            </div>
        <?php endif; ?>

    </section>

</main>

<style>
.dashboard-deposito {
    color: #f1f5f9;
    padding-bottom: 35px;
}

.deposito-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
}

.deposito-eyebrow {
    color: #f4b400;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.7px;
}

.deposito-header h1 {
    margin: 7px 0;
    font-size: 30px;
    font-weight: 700;
}

.deposito-header p {
    color: #94a3b8;
    font-size: 14px;
    line-height: 1.6;
    max-width: 650px;
    margin: 0;
}

.deposito-fecha {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border: 1px solid #263548;
    background: #111c2a;
    border-radius: 12px;
    color: #cbd5e1;
    font-size: 13px;
    white-space: nowrap;
}

.deposito-fecha i {
    color: #f4b400;
}

.deposito-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 25px;
}

.deposito-stat {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px;
    min-width: 0;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 16px;
    transition: .2s ease;
}

.deposito-stat:hover {
    transform: translateY(-3px);
    border-color: #526174;
}

.deposito-stat-icon,
.quick-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 13px;
    font-size: 19px;
}

.blue {
    background: rgba(59,130,246,.13);
    color: #60a5fa;
}

.green {
    background: rgba(34,197,94,.13);
    color: #4ade80;
}

.yellow {
    background: rgba(244,180,0,.13);
    color: #f4b400;
}

.orange {
    background: rgba(249,115,22,.13);
    color: #fb923c;
}

.deposito-stat span {
    display: block;
    color: #94a3b8;
    font-size: 12px;
    margin-bottom: 5px;
}

.deposito-stat h2 {
    margin: 0;
    color: #f8fafc;
    font-size: 26px;
    font-weight: 700;
}

.deposito-stat small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 10px;
}

.deposito-section {
    min-width: 0;
    padding: 22px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 17px;
}

.deposito-section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.deposito-section-title h2 {
    color: #f1f5f9;
    font-size: 17px;
    font-weight: 650;
    margin: 0 0 5px;
}

.deposito-section-title p {
    color: #8493a7;
    font-size: 12px;
    margin: 0;
}

.section-symbol {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(59,130,246,.13);
    color: #60a5fa;
}

.section-symbol.warning {
    background: rgba(249,115,22,.13);
    color: #fb923c;
}

.section-symbol.yellow {
    background: rgba(244,180,0,.13);
    color: #f4b400;
}

.deposito-quick-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 13px;
}

.quick-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #0d1724;
    border: 1px solid #263548;
    border-radius: 13px;
    text-decoration: none;
    transition: .2s ease;
    min-width: 0;
}

.quick-card:hover {
    border-color: #f4b400;
    transform: translateY(-2px);
}

.quick-card > div:nth-child(2) {
    flex: 1;
    min-width: 0;
}

.quick-card strong {
    display: block;
    color: #e2e8f0;
    font-size: 13px;
    margin-bottom: 5px;
}

.quick-card span {
    display: block;
    color: #8493a7;
    font-size: 11px;
    line-height: 1.5;
}

.quick-arrow {
    color: #64748b;
    font-size: 12px;
}

.deposito-columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin: 22px 0;
}

.deposito-list {
    display: flex;
    flex-direction: column;
}

.stock-item,
.request-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid #202d3d;
}

.stock-item:last-child,
.request-item:last-child {
    border-bottom: none;
}

.stock-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: rgba(249,115,22,.12);
    color: #fb923c;
}

.stock-info,
.request-info {
    flex: 1;
    min-width: 0;
}

.stock-info strong,
.request-info strong {
    display: block;
    color: #e2e8f0;
    font-size: 12px;
    font-weight: 600;
}

.stock-info small,
.request-info small {
    display: block;
    color: #8493a7;
    font-size: 11px;
    margin-top: 5px;
}

.stock-value {
    text-align: right;
}

.stock-value strong {
    display: block;
    color: #fb923c;
    font-size: 15px;
}

.stock-value span {
    color: #64748b;
    font-size: 10px;
}

.request-status {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 20px;
    background: rgba(148,163,184,.12);
    color: #cbd5e1;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
}

.request-status.pendiente {
    background: rgba(244,180,0,.12);
    color: #f4b400;
}

.request-status.aprobada {
    background: rgba(34,197,94,.12);
    color: #4ade80;
}

.request-status.rechazada {
    background: rgba(239,68,68,.12);
    color: #f87171;
}

.request-status.entregada {
    background: rgba(59,130,246,.12);
    color: #60a5fa;
}

.section-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding-top: 17px;
    margin-top: 10px;
    border-top: 1px solid #263548;
    color: #f4b400;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
}

.section-link:hover {
    color: #ffd45c;
}

.deposito-table-container {
    width: 100%;
    overflow-x: auto;
}

.deposito-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 450px;
}

.deposito-table th {
    padding: 12px;
    text-align: left;
    color: #8493a7;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 1px solid #263548;
}

.deposito-table td {
    padding: 15px 12px;
    color: #cbd5e1;
    font-size: 12px;
    border-bottom: 1px solid #202d3d;
}

.deposito-table tbody tr:last-child td {
    border-bottom: none;
}

.deposito-table td strong {
    color: #f1f5f9;
}

.deposito-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 150px;
    color: #64748b;
    text-align: center;
}

.deposito-empty i {
    font-size: 25px;
}

.deposito-empty p {
    margin: 0;
    font-size: 12px;
}

@media (max-width: 1150px) {
    .deposito-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 800px) {
    .deposito-columns {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .deposito-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .deposito-header h1 {
        font-size: 25px;
    }

    .deposito-stats {
        grid-template-columns: 1fr;
    }

    .deposito-quick-grid {
        grid-template-columns: 1fr;
    }

    .deposito-section {
        padding: 16px;
    }

    .deposito-stat {
        padding: 17px;
    }
}
</style>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
