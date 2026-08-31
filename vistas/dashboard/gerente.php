<?php

require_once "../../modelos/Dashboard.php";

$dashboard = new Dashboard();
$datos = $dashboard->obtenerDatos();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

$herramientas = $datos["herramientas"];
$auditoria = $datos["auditoria"];
$actividad = $datos["actividad"];

?>

<main class="content">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->
    <div class="page-title">
        <div>
            <h1>Dashboard</h1>
            <p>Panel general de gestión y control de la empresa.</p>
        </div>
        <div class="dashboard-date">
            <i class="fa-regular fa-calendar-days"></i> <?= date("d/m/Y") ?>
        </div>
    </div>

    <!-- =====================================================
         INDICADORES PRINCIPALES (KPIs)
    ====================================================== -->
    <section class="card-grid">

        <!-- OBRAS -->
        <div class="dashboard-card">
            <i class="fa-solid fa-helmet-safety"></i>
            <h3>Obras</h3>
            <h2><?= $datos["obras"]; ?></h2>
            <p>Obras registradas en el sistema</p>
        </div>

        <!-- CLIENTES -->
        <div class="dashboard-card">
            <i class="fa-solid fa-user-group"></i>
            <h3>Clientes</h3>
            <h2><?= $datos["clientes"]; ?></h2>
            <p>Clientes activos</p>
        </div>

        <!-- EMPLEADOS -->
        <div class="dashboard-card">
            <i class="fa-solid fa-users"></i>
            <h3>Empleados</h3>
            <h2><?= $datos["empleados"]; ?></h2>
            <p>Personal activo</p>
        </div>

        <!-- MATERIALES -->
        <div class="dashboard-card">
            <i class="fa-solid fa-boxes-stacked"></i>
            <h3>Materiales</h3>
            <h2><?= $datos["materiales"]; ?></h2>
            <p>Materiales registrados</p>
        </div>

    </section>

    <!-- =====================================================
         SECCIÓN INVENTARIO DE HERRAMIENTAS
    ====================================================== -->
    <section class="dashboard-section">
        <div class="section-heading">
            <h2>Estado de herramientas</h2>
            <p>Situación actual de las unidades registradas.</p>
        </div>

        <div class="inventory-grid">

            <div class="inventory-card">
                <span class="inventory-title">Total</span>
                <strong><?= $herramientas["total"]; ?></strong>
                <small>Unidades</small>
            </div>

            <div class="inventory-card available">
                <span class="inventory-title">Disponibles</span>
                <strong><?= $herramientas["disponibles"]; ?></strong>
                <small>Listas para asignar</small>
            </div>

            <div class="inventory-card assigned">
                <span class="inventory-title">Asignadas</span>
                <strong><?= $herramientas["asignadas"]; ?></strong>
                <small>En obras</small>
            </div>

            <div class="inventory-card repair">
                <span class="inventory-title">En reparación</span>
                <strong><?= $herramientas["reparacion"]; ?></strong>
                <small>Fuera de disponibilidad</small>
            </div>

            <div class="inventory-card disabled">
                <span class="inventory-title">Fuera de servicio</span>
                <strong><?= $herramientas["fuera_servicio"]; ?></strong>
                <small>No disponibles</small>
            </div>

        </div>
    </section>

    <!-- =====================================================
         DOS COLUMNAS: ACTIVIDAD Y RESUMEN
    ====================================================== -->
    <div class="dashboard-columns">

        <!-- ACTIVIDAD DEL SISTEMA -->
        <section class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h2>Actividad del sistema</h2>
                    <p>Registro de operaciones realizadas.</p>
                </div>
                <div class="audit-total">
                    <?= $auditoria["total"]; ?>
                    <span>acciones</span>
                </div>
            </div>

            <div class="audit-stats">
                <div>
                    <strong><?= $auditoria["insertar"]; ?></strong>
                    <span>Registros</span>
                </div>
                <div>
                    <strong><?= $auditoria["editar"]; ?></strong>
                    <span>Ediciones</span>
                </div>
                <div>
                    <strong><?= $auditoria["eliminar"]; ?></strong>
                    <span>Eliminaciones</span>
                </div>
            </div>
        </section>

        <!-- RESUMEN DE INVENTARIO -->
        <section class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h2>Resumen de inventario</h2>
                    <p>Distribución porcentual de herramientas.</p>
                </div>
            </div>

            <?php
            $totalHerramientas = $herramientas["total"];

            if ($totalHerramientas > 0) {
                $porcentajeDisponible = round(($herramientas["disponibles"] / $totalHerramientas) * 100);
                $porcentajeAsignada   = round(($herramientas["asignadas"] / $totalHerramientas) * 100);
                $porcentajeReparacion = round(($herramientas["reparacion"] / $totalHerramientas) * 100);
                $porcentajeFuera      = round(($herramientas["fuera_servicio"] / $totalHerramientas) * 100);
            } else {
                $porcentajeDisponible = $porcentajeAsignada = $porcentajeReparacion = $porcentajeFuera = 0;
            }
            ?>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Disponibles</span>
                    <strong><?= $porcentajeDisponible ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar available-bar" style="width: <?= $porcentajeDisponible ?>%;"></div>
                </div>
            </div>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Asignadas</span>
                    <strong><?= $porcentajeAsignada ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar assigned-bar" style="width: <?= $porcentajeAsignada ?>%;"></div>
                </div>
            </div>

            <div class="progress-item">
                <div class="progress-label">
                    <span>En reparación</span>
                    <strong><?= $porcentajeReparacion ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar repair-bar" style="width: <?= $porcentajeReparacion ?>%;"></div>
                </div>
            </div>

            <div class="progress-item">
                <div class="progress-label">
                    <span>Fuera de servicio</span>
                    <strong><?= $porcentajeFuera ?>%</strong>
                </div>
                <div class="progress">
                    <div class="progress-bar disabled-bar" style="width: <?= $porcentajeFuera ?>%;"></div>
                </div>
            </div>

        </section>

    </div>

    <!-- =====================================================
         ÚLTIMAS OPERACIONES (TABLA)
    ====================================================== -->
    <section class="dashboard-panel">
        <div class="panel-header">
            <div>
                <h2>Últimas operaciones</h2>
                <p>Actividad más reciente registrada por el sistema.</p>
            </div>
        </div>

        <?php if (!empty($actividad)): ?>
            <div class="audit-table-wrapper">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividad as $registro): ?>
                            <tr>
                                <td>
                                    <div class="audit-user">
                                        <div class="audit-avatar">
                                            <?= strtoupper(substr($registro["nombre"] ?? "U", 0, 1)); ?>
                                        </div>
                                        <strong>
                                            <?= htmlspecialchars(trim(($registro["nombre"] ?? "") . " " . ($registro["apellido"] ?? ""))); ?>
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $accion = strtoupper($registro["accion"]);
                                    $claseAccion = strtolower($registro["accion"]);
                                    ?>
                                    <span class="audit-badge <?= $claseAccion; ?>">
                                        <?= htmlspecialchars($accion); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="module-name">
                                        <?= htmlspecialchars($registro["tabla_afectada"] ?? "-"); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($registro["descripcion"] ?? "-"); ?>
                                </td>
                                <td>
                                    <span class="audit-date">
                                        <?= date("d/m/Y H:i", strtotime($registro["fecha"])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-dashboard">
                <strong>No hay actividad registrada.</strong>
                <p>Las operaciones realizadas en el sistema aparecerán aquí.</p>
            </div>
        <?php endif; ?>
    </section>

</main>

<style>
    /* =====================================================
       ESTILOS DEL DASHBOARD (BUILDPRO UI v2.0 INTEGRADO)
    ====================================================== */

    .page-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .page-title h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--white);
    }

    .page-title p {
        color: var(--text-secondary);
        font-size: 14px;
        margin-top: 4px;
    }

    .dashboard-date {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
        background: var(--surface-light);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dashboard-section {
        margin-bottom: 30px;
    }

    .section-heading {
        margin-bottom: 18px;
    }

    .section-heading h2,
    .panel-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--white);
        margin: 0;
    }

    .section-heading p,
    .panel-header p {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    /* GRID Y TARJETAS DE INVENTARIO */
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px;
    }

    .inventory-card {
        background: linear-gradient(180deg, #132133, #101b29);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.25s, border-color 0.25s;
    }

    .inventory-card:hover {
        transform: translateY(-4px);
        border-color: #38526d;
    }

    .inventory-title {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .inventory-card strong {
        font-size: 32px;
        font-weight: 700;
        color: var(--white);
    }

    .inventory-card small {
        font-size: 12px;
        color: var(--text-secondary);
    }

    .inventory-card.available { border-left: 4px solid var(--success); }
    .inventory-card.assigned { border-left: 4px solid var(--warning); }
    .inventory-card.repair { border-left: 4px solid #f97316; }
    .inventory-card.disabled { border-left: 4px solid var(--danger); }

    /* LAYOUT COLUMNAS */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .dashboard-panel {
        background: linear-gradient(180deg, #132133, #101b29);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        box-shadow: var(--shadow-sm);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .audit-total {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary);
        text-align: right;
        line-height: 1;
    }

    .audit-total span {
        display: block;
        font-size: 11px;
        font-weight: 500;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .audit-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .audit-stats > div {
        background: var(--surface-light);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px;
        text-align: center;
    }

    .audit-stats strong {
        display: block;
        font-size: 26px;
        font-weight: 700;
        color: var(--white);
    }

    .audit-stats span {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* BARRAS DE PROGRESO */
    .progress-item {
        margin-bottom: 20px;
    }

    .progress-item:last-child {
        margin-bottom: 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .progress-label strong {
        color: var(--white);
    }

    .progress {
        width: 100%;
        height: 8px;
        background: var(--surface-light);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.4s ease;
    }

    .available-bar { background: var(--success); }
    .assigned-bar { background: var(--warning); }
    .repair-bar { background: #f97316; }
    .disabled-bar { background: var(--danger); }

    /* TABLA DE AUDITORÍA */
    .audit-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .audit-table th {
        text-align: left;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text-secondary);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .audit-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    .audit-table tbody tr:hover {
        background: var(--surface-hover);
    }

    .audit-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .audit-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--surface-light);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: var(--primary);
    }

    .audit-user strong {
        font-size: 13px;
        color: var(--white);
    }

    .audit-badge {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .audit-badge.insertar {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

        .audit-badge.activar{
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }


    .audit-badge.editar {
        background: rgba(245, 158, 11, 0.15);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .audit-badge.eliminar {
        background: rgba(239, 68, 68, 0.15);
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

        .audit-badge.baja{
        background: rgba(239, 68, 68, 0.15);
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .module-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .audit-date {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .empty-dashboard {
        padding: 40px;
        text-align: center;
        color: var(--text-secondary);
    }

    .empty-dashboard strong {
        display: block;
        color: var(--white);
        margin-bottom: 6px;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .dashboard-columns {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .page-title {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        
        .audit-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php

require_once "../../layouts/footer.php";

?>