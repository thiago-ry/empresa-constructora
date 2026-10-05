<?php

session_start();

date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once "../../modelos/Asistencia.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login/");
    exit;
}

$usuario = $_SESSION["usuario"];

$id_usuario_capataz = (int)($usuario["id"] ?? 0);

if ($id_usuario_capataz <= 0) {
    header("Location: ../dashboard/capataz.php");
    exit;
}

$asistenciaModel = new Asistencia();

$id_usuario_empleado = $_GET["id_usuario"] ?? "";
$id_obra = $_GET["id_obra"] ?? "";
$fecha_desde = $_GET["fecha_desde"] ?? "";
$fecha_hasta = $_GET["fecha_hasta"] ?? "";

$id_usuario_empleado = trim($id_usuario_empleado);
$id_obra = trim($id_obra);
$fecha_desde = trim($fecha_desde);
$fecha_hasta = trim($fecha_hasta);

/* Empleados disponibles para el filtro */
$empleados = $asistenciaModel->obtenerEmpleadosCapataz(
    $id_usuario_capataz
);

/* Obras disponibles para el filtro */
$obras = $asistenciaModel->obtenerObrasCapataz(
    $id_usuario_capataz
);

/* Historial */
$historial = $asistenciaModel->obtenerHistorialPorCapataz(
    $id_usuario_capataz,
    $id_usuario_empleado,
    $id_obra,
    $fecha_desde,
    $fecha_hasta
);

/* Resumen */
$resumen = $asistenciaModel->obtenerResumenPorCapataz(
    $id_usuario_capataz,
    $id_usuario_empleado,
    $id_obra,
    $fecha_desde,
    $fecha_hasta
);

$total = (int)($resumen["total"] ?? 0);
$completas = (int)($resumen["completas"] ?? 0);
$sin_salida = (int)($resumen["sin_salida"] ?? 0);
$ausentes = (int)($resumen["ausentes"] ?? 0);
$tardes = (int)($resumen["tardes"] ?? 0);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content historial-page">

    <div class="page-header">

        <div>
            <span class="page-kicker">
                CONTROL DE PERSONAL
            </span>

            <h1>
                Historial de asistencia
            </h1>

            <p>
                Consultá las asistencias registradas de tus obras.
            </p>
        </div>

        <a
            href="../empleado_obra/"
            class="btn-back">

            <i class="fa-solid fa-arrow-left"></i>

            Volver a empleados

        </a>

    </div>

    <!-- FILTROS -->

    <div class="filter-card">

        <form method="GET">

            <div class="filter-group">

                <label>
                    Empleado
                </label>

                <select name="id_usuario">

                    <option value="">
                        Todos los empleados
                    </option>

                    <?php foreach ($empleados as $empleado): ?>

                        <option
                            value="<?= (int)$empleado["id_usuario"] ?>"
                            <?= $id_usuario_empleado == $empleado["id_usuario"] ? "selected" : "" ?>>

                            <?= htmlspecialchars(
                                $empleado["apellido"] . ", " . $empleado["nombre"]
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="filter-group">

                <label>
                    Obra
                </label>

                <select name="id_obra">

                    <option value="">
                        Todas las obras
                    </option>

                    <?php foreach ($obras as $obra): ?>

                        <option
                            value="<?= (int)$obra["id_obra"] ?>"
                            <?= $id_obra == $obra["id_obra"] ? "selected" : "" ?>>

                            <?= htmlspecialchars(
                                $obra["nombre_obra"]
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="filter-group">

                <label>
                    Desde
                </label>

                <input
                    type="date"
                    name="fecha_desde"
                    value="<?= htmlspecialchars($fecha_desde) ?>">

            </div>

            <div class="filter-group">

                <label>
                    Hasta
                </label>

                <input
                    type="date"
                    name="fecha_hasta"
                    value="<?= htmlspecialchars($fecha_hasta) ?>">

            </div>

            <div class="filter-actions">

                <button
                    type="submit"
                    class="btn-filter">

                    <i class="fa-solid fa-filter"></i>

                    Filtrar

                </button>

                <a
                    href="historial.php"
                    class="btn-clear">

                    Limpiar

                </a>

            </div>

        </form>

    </div>

    <!-- RESUMEN -->

    <div class="summary-grid">

        <div class="summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-clipboard-list"></i>

            </div>

            <div>

                <span>
                    Registros
                </span>

                <strong>
                    <?= $total ?>
                </strong>

            </div>

        </div>

        <div class="summary-card">

            <div class="summary-icon complete">

                <i class="fa-solid fa-circle-check"></i>

            </div>

            <div>

                <span>
                    Jornadas completas
                </span>

                <strong>
                    <?= $completas ?>
                </strong>

            </div>

        </div>

        <div class="summary-card">

            <div class="summary-icon pending">

                <i class="fa-solid fa-clock"></i>

            </div>

            <div>

                <span>
                    Sin salida
                </span>

                <strong>
                    <?= $sin_salida ?>
                </strong>

            </div>

        </div>

        <div class="summary-card">

            <div class="summary-icon absent">

                <i class="fa-solid fa-user-xmark"></i>

            </div>

            <div>

                <span>
                    Ausentes
                </span>

                <strong>
                    <?= $ausentes ?>
                </strong>

            </div>

        </div>

        <div class="summary-card">

            <div class="summary-icon late">

                <i class="fa-solid fa-person-circle-exclamation"></i>

            </div>

            <div>

                <span>
                    Tardes
                </span>

                <strong>
                    <?= $tardes ?>
                </strong>

            </div>

        </div>

    </div>

    <!-- TABLA -->

    <div class="table-header">

        <div>

            <h2>
                Registros
            </h2>

            <p>
                <?= count($historial) ?> registros encontrados
            </p>

        </div>

    </div>

    <?php if (empty($historial)): ?>

        <div class="empty-state">

            <div class="empty-icon">

                <i class="fa-solid fa-calendar-xmark"></i>

            </div>

            <h2>
                No hay registros
            </h2>

            <p>
                No se encontraron asistencias con los filtros seleccionados.
            </p>

        </div>

    <?php else: ?>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Empleado
                        </th>

                        <th>
                            Cargo
                        </th>

                        <th>
                            Obra
                        </th>

                        <th>
                            Fecha
                        </th>

                        <th>
                            Entrada
                        </th>

                        <th>
                            Salida
                        </th>

                        <th>
                            Estado
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($historial as $registro): ?>

                        <?php

                        $entrada = !empty($registro["hora_entrada"]);
                        $salida = !empty($registro["hora_salida"]);
                        $estado = $registro["estado"] ?? "";

                        ?>

                        <tr>

                            <td>

                                <div class="employee">

                                    <div class="avatar">

                                        <i class="fa-solid fa-user"></i>

                                    </div>

                                    <div>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $registro["apellido"] . ", " . $registro["nombre"]
                                            ) ?>

                                        </strong>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <span class="cargo">

                                    <i class="fa-solid fa-briefcase"></i>

                                    <?= htmlspecialchars(
                                        $registro["nombre_cargo"] ?? "Sin cargo"
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                <span class="work">

                                    <i class="fa-solid fa-building"></i>

                                    <?= htmlspecialchars(
                                        $registro["nombre_obra"] ?? "Sin obra"
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                <span class="date">

                                    <?= date(
                                        "d/m/Y",
                                        strtotime($registro["fecha"])
                                    ) ?>

                                </span>

                            </td>

                            <td>

                                <?php if ($entrada): ?>

                                    <span class="time">

                                        <?= date(
                                            "H:i",
                                            strtotime($registro["hora_entrada"])
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span class="empty-time">
                                        --:--
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($salida): ?>

                                    <span class="time">

                                        <?= date(
                                            "H:i",
                                            strtotime($registro["hora_salida"])
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span class="empty-time">
                                        --:--
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($estado === "Ausente"): ?>

                                    <span class="badge badge-danger">
                                        Ausente
                                    </span>

                                <?php elseif ($estado === "Tarde"): ?>

                                    <span class="badge badge-late">
                                        Tarde
                                    </span>

                                <?php elseif ($entrada && $salida): ?>

                                    <span class="badge badge-success">
                                        Completa
                                    </span>

                                <?php elseif ($entrada): ?>

                                    <span class="badge badge-warning">
                                        Sin salida
                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-secondary">
                                        Sin registrar
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>

<style>

.historial-page {
    padding: 30px;
}

.historial-page .page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.historial-page .page-kicker {
    display: block;
    color: #f4b400;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    margin-bottom: 6px;
}

.historial-page .page-header h1 {
    margin: 0;
    color: #fff;
    font-size: 30px;
}

.historial-page .page-header p {
    margin: 7px 0 0;
    color: #8d9aaa;
}

.historial-page .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 15px;
    background: #111c2a;
    border: 1px solid #263548;
    border-radius: 10px;
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.historial-page .btn-back:hover {
    border-color: #f4b400;
    color: #f4b400;
}

.historial-page .filter-card {
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 22px;
}

.historial-page .filter-card form {
    display: grid;
    grid-template-columns: 1.4fr 1.2fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
}

.historial-page .filter-group label {
    display: block;
    margin-bottom: 7px;
    color: #8d9aaa;
    font-size: 12px;
    font-weight: 700;
}

.historial-page .filter-group select,
.historial-page .filter-group input {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 13px;
    background: #0b1320;
    border: 1px solid #263548;
    border-radius: 10px;
    color: #fff;
    outline: none;
}

.historial-page .filter-group select:focus,
.historial-page .filter-group input:focus {
    border-color: #f4b400;
}

.historial-page .filter-actions {
    display: flex;
    gap: 8px;
}

.historial-page .btn-filter,
.historial-page .btn-clear {
    padding: 11px 15px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
}

.historial-page .btn-filter {
    border: none;
    background: #f4b400;
    color: #111;
}

.historial-page .btn-filter:hover {
    background: #ffc928;
}

.historial-page .btn-clear {
    background: #0b1320;
    border: 1px solid #263548;
    color: #aeb9c7;
}

.historial-page .btn-clear:hover {
    color: #fff;
}

.historial-page .summary-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-bottom: 28px;
}

.historial-page .summary-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 16px;
}

.historial-page .summary-icon {
    width: 45px;
    height: 45px;
    min-width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(244, 180, 0, .1);
    color: #f4b400;
}

.historial-page .summary-icon.complete {
    background: rgba(34, 197, 94, .1);
    color: #4ade80;
}

.historial-page .summary-icon.pending {
    background: rgba(148, 163, 184, .1);
    color: #94a3b8;
}

.historial-page .summary-icon.absent {
    background: rgba(239, 68, 68, .1);
    color: #f87171;
}

.historial-page .summary-icon.late {
    background: rgba(249, 115, 22, .1);
    color: #fb923c;
}

.historial-page .summary-card span {
    display: block;
    color: #7f8c9d;
    font-size: 12px;
    margin-bottom: 4px;
}

.historial-page .summary-card strong {
    color: #fff;
    font-size: 24px;
}

.historial-page .table-header {
    margin-bottom: 13px;
}

.historial-page .table-header h2 {
    margin: 0;
    color: #fff;
    font-size: 20px;
}

.historial-page .table-header p {
    margin: 4px 0 0;
    color: #7f8c9d;
    font-size: 13px;
}

.historial-page .table-container {
    overflow-x: auto;
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
}

.historial-page table {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
}

.historial-page th {
    padding: 15px 18px;
    text-align: left;
    background: #0d1724;
    color: #7f8c9d;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .8px;
}

.historial-page td {
    padding: 15px 18px;
    border-top: 1px solid #1f2d3d;
    color: #d5dce5;
}

.historial-page .employee {
    display: flex;
    align-items: center;
    gap: 11px;
}

.historial-page .avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1b2939;
    color: #f4b400;
    border-radius: 9px;
}

.historial-page .employee strong {
    color: #fff;
    font-size: 13px;
}

.historial-page .cargo,
.historial-page .work {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #c2ccd7;
    font-size: 13px;
}

.historial-page .cargo i,
.historial-page .work i {
    color: #f4b400;
}

.historial-page .date {
    color: #c2ccd7;
    font-size: 13px;
}

.historial-page .time {
    color: #fff;
    font-size: 14px;
    font-weight: 700;
}

.historial-page .empty-time {
    color: #566678;
    font-weight: 600;
}

.historial-page .badge {
    display: inline-flex;
    align-items: center;
    padding: 7px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.historial-page .badge-success {
    background: rgba(34, 197, 94, .1);
    color: #4ade80;
}

.historial-page .badge-warning {
    background: rgba(244, 180, 0, .1);
    color: #f4b400;
}

.historial-page .badge-danger {
    background: rgba(239, 68, 68, .1);
    color: #f87171;
}

.historial-page .badge-late {
    background: rgba(249, 115, 22, .1);
    color: #fb923c;
}

.historial-page .badge-secondary {
    background: rgba(148, 163, 184, .1);
    color: #94a3b8;
}

.historial-page .empty-state {
    padding: 60px 25px;
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
    text-align: center;
}

.historial-page .empty-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 180, 0, .1);
    color: #f4b400;
    border-radius: 16px;
    font-size: 25px;
}

.historial-page .empty-state h2 {
    margin: 0;
    color: #fff;
    font-size: 20px;
}

.historial-page .empty-state p {
    margin: 8px 0 0;
    color: #7f8c9d;
}

@media (max-width: 1200px) {

    .historial-page .filter-card form {
        grid-template-columns: 1fr 1fr;
    }

    .historial-page .filter-actions {
        grid-column: span 2;
    }

    .historial-page .summary-grid {
        grid-template-columns: repeat(3, 1fr);
    }

}

@media (max-width: 700px) {

    .historial-page {
        padding: 20px;
    }

    .historial-page .page-header {
        flex-direction: column;
        align-items: stretch;
    }

    .historial-page .summary-grid {
        grid-template-columns: 1fr;
    }

    .historial-page .filter-card form {
        grid-template-columns: 1fr;
    }

    .historial-page .filter-actions {
        grid-column: auto;
    }

}

</style>