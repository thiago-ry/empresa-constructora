
<?php

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

$obra = $obraCapataz ?? null;
$resumen = $resumenCapataz ?? [];

$asistencias = $asistenciasHoy ?? [];
$avances = $ultimosAvances ?? [];
$tareas = $tareasCapataz ?? [];
$incidencias = $incidenciasCapataz ?? [];

$urls = $urlsCapataz ?? [];

function escaparDashboard($valor)
{
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

?>

<main class="content">

<div class="cd-container">

    <!-- ENCABEZADO -->

    <header class="cd-header">

        <div>
            <h1>Dashboard</h1>
            <p>Panel de control de tu obra y equipo de trabajo.</p>
        </div>

        <div class="cd-date">
            <i class="fas fa-calendar-alt"></i>
            <?= date("d/m/Y") ?>
        </div>

    </header>

    <?php if (!$obra): ?>

        <section class="cd-empty">
            <i class="fas fa-hard-hat"></i>
            <h2>No tenés una obra asignada</h2>
            <p>Cuando se te asigne una obra, vas a poder administrar y consultar su información desde este panel.</p>
        </section>

    <?php else: ?>

        <!-- OBRA ASIGNADA -->

        <section class="cd-obra">

            <div class="cd-obra-icon">
                <i class="fas fa-building"></i>
            </div>

            <div class="cd-obra-info">

                <span>OBRA ASIGNADA</span>

                <h2>
                    <?= escaparDashboard($obra["nombre_obra"] ?? "Mi obra") ?>
                </h2>

                <p>
                    <i class="fas fa-map-marker-alt"></i>
                    <?= escaparDashboard($obra["ubicacion"] ?? $obra["direccion"] ?? "Ubicación no disponible") ?>
                </p>

            </div>

            <div class="cd-obra-status">
                <span>Estado actual</span>
                <strong>
                    <?= escaparDashboard($obra["estado"] ?? "Sin estado") ?>
                </strong>
            </div>

        </section>

        <!-- ESTADÍSTICAS PRINCIPALES -->

        <section class="cd-section">

            <div class="cd-section-heading">
                <h2>Resumen general</h2>
                <p>Información actual de tu obra.</p>
            </div>

            <div class="cd-grid">

                <article class="cd-card">

                    <div class="cd-icon blue">
                        <i class="fas fa-users"></i>
                    </div>

                    <div>
                        <span>Personal activo</span>
                        <h3><?= (int)($resumen["empleados"] ?? 0) ?></h3>
                        <small>Empleados asignados</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon green">
                        <i class="fas fa-user-check"></i>
                    </div>

                    <div>
                        <span>Presentes hoy</span>
                        <h3><?= (int)($resumen["presentes"] ?? 0) ?></h3>
                        <small>Personal registrado</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon red">
                        <i class="fas fa-user-times"></i>
                    </div>

                    <div>
                        <span>Ausentes hoy</span>
                        <h3><?= (int)($resumen["ausentes"] ?? 0) ?></h3>
                        <small>Inasistencias registradas</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div>
                        <span>Sin salida</span>
                        <h3><?= (int)($resumen["sin_salida"] ?? 0) ?></h3>
                        <small>Registros incompletos</small>
                    </div>

                </article>

            </div>

        </section>

        <!-- SEGUNDO RESUMEN -->

        <section class="cd-section">

            <div class="cd-section-heading">
                <h2>Gestión de obra</h2>
                <p>Seguimiento de tareas, avances e incidencias.</p>
            </div>

            <div class="cd-grid">

                <article class="cd-card">

                    <div class="cd-icon purple">
                        <i class="fas fa-tasks"></i>
                    </div>

                    <div>
                        <span>Tareas registradas</span>
                        <h3><?= (int)($resumen["tareas"] ?? 0) ?></h3>
                        <small>Tareas de la obra</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon blue">
                        <i class="fas fa-chart-line"></i>
                    </div>

                    <div>
                        <span>Avances registrados</span>
                        <h3><?= (int)($resumen["avances"] ?? 0) ?></h3>
                        <small>Registros de progreso</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>

                    <div>
                        <span>Incidencias</span>
                        <h3><?= (int)($resumen["incidencias"] ?? 0) ?></h3>
                        <small>Total registrado</small>
                    </div>

                </article>

                <article class="cd-card">

                    <div class="cd-icon orange">
                        <i class="fas fa-tools"></i>
                    </div>

                    <div>
                        <span>Solicitudes pendientes</span>
                        <h3><?= (int)($resumen["solicitudes_pendientes"] ?? 0) ?></h3>
                        <small>Solicitudes de herramientas</small>
                    </div>

                </article>

            </div>

        </section>

        <section class="cd-tables">

            <!-- ASISTENCIAS -->

            <div class="cd-panel">

                <div class="cd-panel-header">
                    <div>
                        <h2>Asistencias de hoy</h2>
                        <p>Últimos registros del personal.</p>
                    </div>
                </div>

                <div class="cd-table-wrapper">

                    <table class="cd-table">

                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (empty($asistencias)): ?>

                            <tr>
                                <td colspan="4" class="cd-empty-row">
                                    No hay asistencias registradas hoy.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($asistencias as $asistencia): ?>

                                <?php
                                $estado = $asistencia["estado"] ?? "";

                                $clase = "neutral";

                                if ($estado === "Presente") {
                                    $clase = "success";
                                } elseif ($estado === "Ausente") {
                                    $clase = "danger";
                                } elseif ($estado === "Tarde") {
                                    $clase = "warning";
                                }
                                ?>

                                <tr>

                                    <td>
                                        <?= escaparDashboard(
                                            ($asistencia["nombre"] ?? "") . " " .
                                            ($asistencia["apellido"] ?? "")
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= escaparDashboard($asistencia["hora_entrada"] ?? "--:--") ?>
                                    </td>

                                    <td>
                                        <?= escaparDashboard($asistencia["hora_salida"] ?? "--:--") ?>
                                    </td>

                                    <td>
                                        <span class="cd-status <?= $clase ?>">
                                            <?= escaparDashboard($estado ?: "Sin estado") ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- INCIDENCIAS -->

            <div class="cd-panel">

                <div class="cd-panel-header">
                    <div>
                        <h2>Últimas incidencias</h2>
                        <p>Problemas registrados en la obra.</p>
                    </div>
                </div>

                <div class="cd-table-wrapper">

                    <table class="cd-table">

                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (empty($incidencias)): ?>

                            <tr>
                                <td colspan="3" class="cd-empty-row">
                                    No hay incidencias registradas.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($incidencias as $incidencia): ?>

                                <tr>

                                    <td>
                                        <?= !empty($incidencia["fecha"])
                                            ? date("d/m/Y", strtotime($incidencia["fecha"]))
                                            : "--" ?>
                                    </td>

                                    <td>
                                        <?= escaparDashboard($incidencia["tipo_incidencia"] ?? "") ?>
                                    </td>

                                    <td>
                                        <?= escaparDashboard($incidencia["estado"] ?? "") ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

        <!-- ÚLTIMOS AVANCES -->

        <section class="cd-panel cd-advances">

            <div class="cd-panel-header">
                <div>
                    <h2>Últimos avances de obra</h2>
                    <p>Registros de progreso realizados.</p>
                </div>

            </div>

            <?php if (empty($avances)): ?>

                <div class="cd-empty-row">
                    Todavía no hay avances registrados.
                </div>

            <?php else: ?>

                <div class="cd-advance-list">

                    <?php foreach ($avances as $avance): ?>

                        <article class="cd-advance-item">

                            <div class="cd-advance-icon">
                                <i class="fas fa-hard-hat"></i>
                            </div>

                            <div>
                                <strong>
                                    <?= !empty($avance["fecha"])
                                        ? date("d/m/Y", strtotime($avance["fecha"]))
                                        : "Fecha no disponible" ?>
                                </strong>

                                <p>
                                    <?= escaparDashboard($avance["descripcion"] ?? "Sin descripción") ?>
                                </p>
                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</div>

</main>

<style>

.cd-container {
    padding: 25px;
    color: #f1f5f9;
}

.cd-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.cd-header h1 {
    margin: 0 0 6px;
    font-size: 27px;
    font-weight: 700;
}

.cd-header p,
.cd-section-heading p,
.cd-panel-header p {
    color: #94a3b8;
    margin: 0;
    font-size: 13px;
}

.cd-date {
    padding: 11px 15px;
    border: 1px solid #29394b;
    background: #111c2a;
    border-radius: 10px;
    color: #cbd5e1;
    font-size: 13px;
    white-space: nowrap;
}

.cd-date i {
    color: #f4b400;
    margin-right: 7px;
}

.cd-obra {
    display: flex;
    align-items: center;
    gap: 18px;
    background: linear-gradient(110deg, #17283a, #111c2a);
    border: 1px solid #29394b;
    border-radius: 15px;
    padding: 23px;
    margin-bottom: 28px;
}

.cd-obra-icon {
    width: 58px;
    height: 58px;
    min-width: 58px;
    display: grid;
    place-items: center;
    background: #f4b400;
    color: #111827;
    border-radius: 13px;
    font-size: 23px;
}

.cd-obra-info > span {
    color: #f4b400;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
}

.cd-obra-info h2 {
    margin: 5px 0;
    font-size: 21px;
}

.cd-obra-info p {
    color: #94a3b8;
    margin: 0;
    font-size: 12px;
}

.cd-obra-info p i {
    margin-right: 5px;
}

.cd-obra-status {
    margin-left: auto;
    text-align: right;
}

.cd-obra-status span {
    display: block;
    color: #94a3b8;
    font-size: 11px;
    margin-bottom: 5px;
}

.cd-obra-status strong {
    color: #f4b400;
    font-size: 13px;
}

.cd-section {
    margin-bottom: 28px;
}

.cd-section-heading {
    margin-bottom: 14px;
}

.cd-section-heading h2,
.cd-panel-header h2 {
    font-size: 17px;
    margin: 0 0 5px;
}

.cd-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
}

.cd-card {
    display: flex;
    align-items: center;
    gap: 13px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 13px;
    padding: 18px;
    min-width: 0;
}

.cd-icon {
    width: 45px;
    height: 45px;
    min-width: 45px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    font-size: 18px;
}

.cd-icon.blue {
    background: #1e3a5f;
    color: #60a5fa;
}

.cd-icon.green {
    background: #16452f;
    color: #4ade80;
}

.cd-icon.red {
    background: #512b31;
    color: #f87171;
}

.cd-icon.orange {
    background: #49391b;
    color: #fbbf24;
}

.cd-icon.purple {
    background: #39264f;
    color: #c084fc;
}

.cd-card span {
    color: #aab8c8;
    font-size: 11px;
    display: block;
}

.cd-card h3 {
    margin: 4px 0;
    font-size: 24px;
}

.cd-card small {
    color: #718096;
    font-size: 10px;
}

.cd-actions {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.cd-action {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 11px;
    padding: 15px;
    text-decoration: none;
    color: #cbd5e1;
    font-size: 12px;
    transition: .2s;
}

.cd-action i {
    color: #f4b400;
    font-size: 17px;
}

.cd-action:hover {
    border-color: #f4b400;
    background: #17283a;
    color: #fff;
    transform: translateY(-2px);
}

.cd-tables {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 17px;
    margin: 28px 0;
}

.cd-panel {
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 14px;
    padding: 20px;
    min-width: 0;
}

.cd-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 17px;
}

.cd-panel-header a {
    color: #f4b400;
    font-size: 11px;
    text-decoration: none;
    white-space: nowrap;
}

.cd-panel-header a:hover {
    color: #ffd45c;
}

.cd-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.cd-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

.cd-table th {
    color: #94a3b8;
    text-align: left;
    padding: 12px 9px;
    border-bottom: 1px solid #29394b;
    white-space: nowrap;
}

.cd-table td {
    color: #cbd5e1;
    padding: 13px 9px;
    border-bottom: 1px solid #202e3e;
}

.cd-table tbody tr:last-child td {
    border-bottom: none;
}

.cd-empty-row {
    color: #718096 !important;
    text-align: center;
    padding: 25px !important;
    font-size: 12px;
}

.cd-status {
    display: inline-block;
    border-radius: 20px;
    padding: 5px 9px;
    font-size: 10px;
    white-space: nowrap;
}

.cd-status.success {
    background: #16452f;
    color: #4ade80;
}

.cd-status.danger {
    background: #512b31;
    color: #f87171;
}

.cd-status.warning {
    background: #49391b;
    color: #fbbf24;
}

.cd-status.neutral {
    background: #263548;
    color: #cbd5e1;
}

.cd-advances {
    margin-bottom: 25px;
}

.cd-advance-list {
    display: flex;
    flex-direction: column;
}

.cd-advance-item {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 15px 0;
    border-bottom: 1px solid #263548;
}

.cd-advance-item:last-child {
    border-bottom: none;
}

.cd-advance-icon {
    width: 37px;
    height: 37px;
    min-width: 37px;
    display: grid;
    place-items: center;
    background: #49391b;
    color: #f4b400;
    border-radius: 9px;
}

.cd-advance-item strong {
    font-size: 12px;
}

.cd-advance-item p {
    color: #94a3b8;
    font-size: 12px;
    margin: 5px 0 0;
}

.cd-empty {
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 15px;
    padding: 60px 20px;
    text-align: center;
}

.cd-empty > i {
    color: #f4b400;
    font-size: 42px;
    margin-bottom: 15px;
}

.cd-empty h2 {
    font-size: 20px;
}

.cd-empty p {
    color: #94a3b8;
    font-size: 13px;
}

@media(max-width: 1150px) {
    .cd-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cd-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media(max-width: 850px) {
    .cd-tables {
        grid-template-columns: 1fr;
    }
}

@media(max-width: 550px) {
    .cd-container {
        padding: 15px;
    }

    .cd-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .cd-obra {
        flex-wrap: wrap;
    }

    .cd-obra-status {
        width: 100%;
        margin-left: 76px;
        text-align: left;
    }

    .cd-grid,
    .cd-actions {
        grid-template-columns: 1fr;
    }
}

</style>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>
