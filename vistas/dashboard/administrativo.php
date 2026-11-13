
<?php
require_once __DIR__ . "/../../controladores/DashboardController.php";

$controller = new DashboardController();
$datos = $controller->obtenerDatos();

function escapar($valor)
{
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

?>

<main class="content dashboard-admin">

    <section class="dashboard-header">
        <div>
            <span class="dashboard-eyebrow">PANEL ADMINISTRATIVO</span>
            <h1>Dashboard</h1>
            <p>Bienvenido a BUILDPRO. Desde aquí podés consultar la información general de clientes, empleados y presupuestos.</p>
        </div>

        <div class="dashboard-date">
            <i class="fa-solid fa-calendar-days"></i>
            <?= date("d/m/Y") ?>
        </div>
    </section>

    <!-- ESTADÍSTICAS -->
    <section class="stats-grid">

        <article class="stat-card">
            <div class="stat-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <span>Total de clientes</span>
                <h2><?= $datos["total_clientes"] ?></h2>
                <small><?= $datos["clientes_activos"] ?> clientes activos</small>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <span>Total de empleados</span>
                <h2><?= $datos["total_empleados"] ?></h2>
                <small><?= $datos["empleados_activos"] ?> empleados activos</small>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon yellow">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <span>Presupuestos registrados</span>
                <h2><?= $datos["total_presupuestos"] ?></h2>
                <small>Presupuestos del sistema</small>
            </div>
        </article>

    </section>

    <!-- PRESUPUESTOS -->
    <section class="dashboard-section">
        <div class="section-heading">
            <div>
                <h2>Últimos presupuestos</h2>
                <p>Presupuestos registrados recientemente.</p>
            </div>
            <span class="section-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </span>
        </div>

        <div class="table-container">
            <?php if (!empty($datos["presupuestos"])): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Presupuesto</th>
                            <th>Obra</th>
                            <th>Costo total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos["presupuestos"] as $presupuesto): ?>
                            <tr>
                                <td>
                                    <div class="table-primary">
                                        <?= escapar($presupuesto["nombre_presupuesto"] ?? "Sin nombre") ?>
                                    </div>
                                    <small class="table-secondary">
                                        N.º <?= escapar($presupuesto["id_presupuesto"] ?? "") ?>
                                    </small>
                                </td>
                                <td><?= escapar($presupuesto["nombre_obra"] ?? "Sin obra") ?></td>
                                <td class="amount">
                                    $<?= number_format((float)($presupuesto["costo_total"] ?? 0), 2, ",", ".") ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                    <p>No hay presupuestos registrados.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CLIENTES Y EMPLEADOS -->
    <section class="dashboard-columns">

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Clientes</h2>
                    <p>Clientes registrados en BUILDPRO.</p>
                </div>
                <span class="section-icon blue">
                    <i class="fa-solid fa-user-group"></i>
                </span>
            </div>

            <div class="list-container">
                <?php if (!empty($datos["clientes"])): ?>
                    <?php foreach ($datos["clientes"] as $cliente): ?>
                        <div class="person-item">
                            <div class="person-avatar">
                                <?= strtoupper(substr(escapar($cliente["nombre"] ?? "C"), 0, 1)) ?>
                            </div>

                            <div class="person-info">
                                <strong>
                                    <?= escapar(($cliente["nombre"] ?? "") . " " . ($cliente["apellido"] ?? "")) ?>
                                </strong>
                                <small><?= escapar($cliente["correo"] ?? "Sin correo registrado") ?></small>
                            </div>

                            <span class="person-status <?= (int)($cliente["estado"] ?? 0) === 1 ? "active" : "inactive" ?>">
                                <?= (int)($cliente["estado"] ?? 0) === 1 ? "Activo" : "Inactivo" ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-users"></i>
                        <p>No hay clientes registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Empleados</h2>
                    <p>Personal registrado en la empresa.</p>
                </div>
                <span class="section-icon green">
                    <i class="fa-solid fa-user-tie"></i>
                </span>
            </div>

            <div class="list-container">
                <?php if (!empty($datos["empleados"])): ?>
                    <?php foreach ($datos["empleados"] as $empleado): ?>
                        <div class="person-item">
                            <div class="person-avatar green-avatar">
                                <?= strtoupper(substr(escapar($empleado["nombre"] ?? "E"), 0, 1)) ?>
                            </div>

                            <div class="person-info">
                                <strong>
                                    <?= escapar(($empleado["nombre"] ?? "") . " " . ($empleado["apellido"] ?? "")) ?>
                                </strong>
                                <small><?= escapar($empleado["cargos"] ?? "Sin cargo asignado") ?></small>
                            </div>

                            <span class="person-status <?= (int)($empleado["estado"] ?? 0) === 1 ? "active" : "inactive" ?>">
                                <?= (int)($empleado["estado"] ?? 0) === 1 ? "Activo" : "Inactivo" ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-user-tie"></i>
                        <p>No hay empleados registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

</main>

<style>
.dashboard-admin {
    padding-bottom: 35px;
    color: #f1f5f9;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.dashboard-eyebrow {
    color: #f4b400;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.8px;
}

.dashboard-header h1 {
    font-size: 30px;
    font-weight: 700;
    margin: 7px 0;
}

.dashboard-header p {
    color: #94a3b8;
    font-size: 14px;
    margin: 0;
    max-width: 650px;
    line-height: 1.6;
}

.dashboard-date {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 12px;
    color: #cbd5e1;
    font-size: 13px;
    white-space: nowrap;
}

.dashboard-date i {
    color: #f4b400;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 17px;
    padding: 23px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 17px;
    transition: .25s ease;
    min-width: 0;
}

.stat-card:hover {
    transform: translateY(-3px);
    border-color: #526174;
}

.stat-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-size: 20px;
}

.stat-icon.blue,
.section-icon.blue {
    background: rgba(59, 130, 246, .13);
    color: #60a5fa;
}

.stat-icon.green,
.section-icon.green {
    background: rgba(34, 197, 94, .13);
    color: #4ade80;
}

.stat-icon.yellow {
    background: rgba(244, 180, 0, .13);
    color: #f4b400;
}

.stat-info {
    min-width: 0;
}

.stat-info span {
    display: block;
    color: #94a3b8;
    font-size: 13px;
    margin-bottom: 5px;
}

.stat-info h2 {
    font-size: 27px;
    margin: 0;
    font-weight: 700;
    color: #f8fafc;
}

.stat-info small {
    display: block;
    color: #64748b;
    font-size: 11px;
    margin-top: 5px;
}

.dashboard-section {
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 17px;
    padding: 22px;
    min-width: 0;
}

.section-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.section-heading h2 {
    font-size: 17px;
    margin: 0 0 5px;
    font-weight: 650;
}

.section-heading p {
    color: #8493a7;
    font-size: 12px;
    margin: 0;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 180, 0, .12);
    color: #f4b400;
}

.table-container {
    width: 100%;
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 500px;
}

.admin-table th {
    color: #8493a7;
    font-size: 11px;
    font-weight: 600;
    text-align: left;
    padding: 12px;
    border-bottom: 1px solid #263548;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.admin-table td {
    padding: 15px 12px;
    border-bottom: 1px solid #202d3d;
    font-size: 13px;
    color: #cbd5e1;
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

.admin-table tbody tr:hover {
    background: rgba(255, 255, 255, .025);
}

.table-primary {
    color: #f1f5f9;
    font-weight: 600;
}

.table-secondary {
    color: #64748b;
    font-size: 11px;
    display: block;
    margin-top: 4px;
}

.amount {
    color: #f4b400 !important;
    font-weight: 700;
    white-space: nowrap;
}

.dashboard-columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin-top: 25px;
}

.list-container {
    display: flex;
    flex-direction: column;
}

.person-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 0;
    border-bottom: 1px solid #202d3d;
    min-width: 0;
}

.person-item:last-child {
    border-bottom: none;
}

.person-avatar {
    width: 39px;
    height: 39px;
    min-width: 39px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(59, 130, 246, .14);
    color: #60a5fa;
    font-size: 15px;
    font-weight: 700;
}

.green-avatar {
    background: rgba(34, 197, 94, .13);
    color: #4ade80;
}

.person-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    flex: 1;
}

.person-info strong {
    color: #e2e8f0;
    font-size: 13px;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.person-info small {
    color: #8493a7;
    font-size: 11px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.person-status {
    font-size: 10px;
    font-weight: 600;
    padding: 5px 9px;
    border-radius: 20px;
    white-space: nowrap;
}

.person-status.active {
    background: rgba(34, 197, 94, .12);
    color: #4ade80;
}

.person-status.inactive {
    background: rgba(239, 68, 68, .12);
    color: #f87171;
}

.empty-state {
    text-align: center;
    padding: 35px 15px;
    color: #64748b;
}

.empty-state i {
    font-size: 27px;
    margin-bottom: 12px;
    opacity: .7;
}

.empty-state p {
    font-size: 13px;
    margin: 0;
}

@media (max-width: 1050px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .stats-grid .stat-card:last-child {
        grid-column: span 2;
    }
}

@media (max-width: 750px) {
    .dashboard-columns {
        grid-template-columns: 1fr;
    }

    .dashboard-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-date {
        align-self: flex-start;
    }
}

@media (max-width: 520px) {
    .dashboard-admin {
        padding: 0 0 25px;
    }

    .dashboard-header h1 {
        font-size: 25px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .stats-grid .stat-card:last-child {
        grid-column: auto;
    }

    .stat-card {
        padding: 18px;
    }

    .dashboard-section {
        padding: 16px;
    }

    .dashboard-columns {
        gap: 15px;
    }
}
</style>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
