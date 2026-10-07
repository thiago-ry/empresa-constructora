<?php

session_start();

require_once "../../modelos/Asistencia.php";
require_once "../../config/permisos.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login/");
    exit;
}

if (function_exists('verificarPermiso')) {
    verificarPermiso("asistencia");
}

$usuario = $_SESSION["usuario"];

$id_usuario_empleado = (int)($usuario["id"] ?? $usuario["id_usuario"] ?? 0);

if ($id_usuario_empleado <= 0) {
    header("Location: ../dashboard/");
    exit;
}

$asistenciaModel = new Asistencia();

/* Filtros */
$id_obra = (int)($_GET["id_obra"] ?? 0);
$fecha_desde = trim($_GET["fecha_desde"] ?? "");
$fecha_hasta = trim($_GET["fecha_hasta"] ?? "");

/*
 * IMPORTANTE:
 * Acá NO se permite elegir empleado.
 * El empleado siempre es el usuario que inició sesión.
 */

$historial = $asistenciaModel->obtenerHistorialPorEmpleado(
    $id_usuario_empleado,
    $id_obra > 0 ? $id_obra : "",
    $fecha_desde,
    $fecha_hasta
);

$obras = $asistenciaModel->obtenerObrasPorEmpleado($id_usuario_empleado);

/*
 * Datos básicos del empleado
 */
$nombreEmpleado = trim(
    ($usuario["nombre"] ?? "") . " " . ($usuario["apellido"] ?? "")
);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <span class="page-kicker">CONTROL DE PERSONAL</span>
            <h1>Mi historial de asistencia</h1>
            <p>Consultá tus registros de entrada y salida.</p>
        </div>

        <a href="../dashboard/" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver al Inicio
        </a>
    </div>

    <!-- INFORMACIÓN DEL EMPLEADO -->
    <div class="employee-card">
        <div class="employee-icon">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <span class="label">EMPLEADO</span>
            <h2><?= htmlspecialchars($nombreEmpleado) ?></h2>
            <p>
                <i class="fa-solid fa-calendar-check"></i>
                Historial personal de asistencia
            </p>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card-panel">
        <div class="card-header">
            <div>
                <span class="label">FILTROS</span>
                <h2>Buscar asistencia</h2>
            </div>
            <div class="header-icon">
                <i class="fa-solid fa-filter"></i>
            </div>
        </div>

        <form method="GET" class="filter-form">
            <div class="form-grid">

                <div class="form-group">
                    <label for="id_obra">Obra</label>
                    <select name="id_obra" id="id_obra" class="form-control">
                        <option value="">Todas las obras</option>
                        <?php foreach ($obras as $obra): ?>
                            <option
                                value="<?= (int)$obra["id_obra"] ?>"
                                <?= $id_obra == $obra["id_obra"] ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($obra["nombre_obra"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_desde">Desde</label>
                    <input
                        type="date"
                        name="fecha_desde"
                        id="fecha_desde"
                        class="form-control"
                        value="<?= htmlspecialchars($fecha_desde) ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Hasta</label>
                    <input
                        type="date"
                        name="fecha_hasta"
                        id="fecha_hasta"
                        class="form-control"
                        value="<?= htmlspecialchars($fecha_hasta) ?>"
                    >
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter"></i>
                    Filtrar
                </button>

                <a href="asistencia_personal.php" class="btn-secondary">
                    <i class="fa-solid fa-rotate-left"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- TABLA DE HISTORIAL -->
    <div class="card-panel">
        <div class="card-header">
            <div>
                <span class="label">HISTORIAL</span>
                <h2>Mis asistencias</h2>
            </div>
            <div class="header-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
        </div>

        <?php if (empty($historial)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-calendar-xmark empty-icon"></i>
                <h3>No hay asistencias registradas</h3>
                <p>No se encontraron registros de asistencia con los filtros seleccionados.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Obra</th>
                            <th>Estado</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $registro): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= date("d/m/Y", strtotime($registro["fecha"])) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($registro["nombre_obra"]) ?>
                                </td>

                                <td>
                                    <?php
                                    $estado = $registro["estado"] ?? "";
                                    $claseBadge = "badge-neutral";

                                    if ($estado === "Presente") {
                                        $claseBadge = "badge-success";
                                    } elseif ($estado === "Ausente") {
                                        $claseBadge = "badge-danger";
                                    } elseif ($estado === "Tarde") {
                                        $claseBadge = "badge-warning";
                                    }
                                    ?>
                                    <span class="badge <?= $claseBadge ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= !empty($registro["hora_entrada"])
                                        ? htmlspecialchars(date("H:i", strtotime($registro["hora_entrada"])))
                                        : "--:--"
                                    ?>
                                </td>

                                <td>
                                    <?= !empty($registro["hora_salida"])
                                        ? htmlspecialchars(date("H:i", strtotime($registro["hora_salida"])))
                                        : "--:--"
                                    ?>
                                </td>

                                <td>
                                    <?= !empty($registro["observacion"])
                                        ? htmlspecialchars($registro["observacion"])
                                        : "-"
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</main>

<style>
/* --------------------------------------------------------------------------
   HEADER & LAYOUT GENERAL
-------------------------------------------------------------------------- */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.page-kicker {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    color: #f4b400;
    margin-bottom: 6px;
}

.page-header h1 {
    margin: 0;
    color: #fff;
    font-size: 30px;
}

.page-header p {
    margin: 7px 0 0;
    color: #8d9aaa;
}

/* --------------------------------------------------------------------------
   TARJETA DE EMPLEADO
-------------------------------------------------------------------------- */
.employee-card {
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
    padding: 20px 25px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.employee-icon {
    width: 54px;
    height: 54px;
    background: rgba(244, 180, 0, 0.12);
    border: 1px solid rgba(244, 180, 0, 0.3);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f4b400;
    font-size: 22px;
    flex-shrink: 0;
}

.employee-card h2 {
    margin: 4px 0;
    color: #fff;
    font-size: 22px;
}

.employee-card p {
    margin: 0;
    color: #8d9aaa;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* --------------------------------------------------------------------------
   BOTONES
-------------------------------------------------------------------------- */
.btn-primary,
.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #f4b400;
    color: #111;
}

.btn-primary:hover {
    background: #ffc928;
}

.btn-secondary {
    background: #111c2a;
    color: #fff;
    border-color: #263548;
}

.btn-secondary:hover {
    border-color: #f4b400;
    color: #f4b400;
}

/* --------------------------------------------------------------------------
   TARJETAS Y PANELES
-------------------------------------------------------------------------- */
.card-panel {
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 25px;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #f4b400;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.card-header h2 {
    margin: 0;
    color: #fff;
    font-size: 20px;
}

.header-icon {
    width: 42px;
    height: 42px;
    background: #0b1320;
    border: 1px solid #1f2d3d;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7f8c9d;
    font-size: 16px;
}

/* --------------------------------------------------------------------------
   FORMULARIO Y FILTROS
-------------------------------------------------------------------------- */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    color: #7f8c9d;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.form-control {
    width: 100%;
    padding: 11px 14px;
    background: #0b1320;
    border: 1px solid #1f2d3d;
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #f4b400;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* --------------------------------------------------------------------------
   TABLAS Y BADGES
-------------------------------------------------------------------------- */
.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.data-table th,
.data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #1f2d3d;
}

.data-table th {
    color: #7f8c9d;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.data-table td {
    color: #aeb9c7;
    font-size: 14px;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
}

.badge-success {
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #4ade80;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #f87171;
}

.badge-warning {
    background: rgba(244, 180, 0, 0.12);
    border: 1px solid rgba(244, 180, 0, 0.3);
    color: #f4b400;
}

.badge-neutral {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

/* --------------------------------------------------------------------------
   ESTADO VACÍO
-------------------------------------------------------------------------- */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    font-size: 40px;
    color: #263548;
    margin-bottom: 12px;
}

.empty-state h3 {
    color: #fff;
    margin: 0 0 8px;
    font-size: 18px;
}

.empty-state p {
    color: #7f8c9d;
    margin: 0;
    font-size: 14px;
}

/* --------------------------------------------------------------------------
   RESPONSIVE
-------------------------------------------------------------------------- */
@media (max-width: 600px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .employee-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
    }
}
</style>