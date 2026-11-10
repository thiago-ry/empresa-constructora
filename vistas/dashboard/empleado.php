
<?php

session_start();

require_once "../../modelos/Tarea.php";

// Verificar sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: /empresa_constructora/vistas/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

$id_rol = (int) ($usuario["id_rol"] ?? 0);
$id_usuario = (int) ($usuario["id_usuario"] ?? $usuario["id"] ?? 0);

// Solo empleados
if ($id_rol !== 1 || $id_usuario <= 0) {
    http_response_code(403);
    exit("Acceso denegado.");
}

// Nombre del empleado
$nombre = $usuario["nombre"]
    ?? $usuario["nombre_usuario"]
    ?? $usuario["usuario"]
    ?? "Empleado";

// Obtener tareas
$tareaModelo = new Tarea();
$tareas = $tareaModelo->listarEmpleado($id_usuario);

$tareas = is_array($tareas) ? $tareas : [];

function h($valor)
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

// Estadísticas
$totalTareas = count($tareas);
$pendientes = 0;
$enProceso = 0;
$porRevisar = 0;
$completadas = 0;
$vencidas = 0;

$hoy = strtotime(date("Y-m-d"));

$proximasTareas = [];

foreach ($tareas as $tarea) {

    $estado = $tarea["estado_asignacion"] ?? "";

    switch ($estado) {

        case "Pendiente":
            $pendientes++;
            break;

        case "En proceso":
            $enProceso++;
            break;

        case "Por revisar":
            $porRevisar++;
            break;

        case "Completada":
            $completadas++;
            break;
    }

    $fechaLimite = $tarea["fecha_limite"] ?? "";

    if (!empty($fechaLimite)) {

        $fechaTimestamp = strtotime($fechaLimite);

        if ($fechaTimestamp !== false) {

            if (
                $fechaTimestamp < $hoy &&
                $estado !== "Completada"
            ) {
                $vencidas++;
            }

            if (
                $fechaTimestamp >= $hoy &&
                $estado !== "Completada"
            ) {
                $proximasTareas[] = $tarea;
            }
        }
    }
}

// Ordenar próximas tareas por fecha límite
usort($proximasTareas, function ($a, $b) {

    return strtotime($a["fecha_limite"])
        <=> strtotime($b["fecha_limite"]);

});

// Mostrar solamente las primeras
$proximasTareas = array_slice($proximasTareas, 0, 4);

// Primeras tareas del listado
$tareasRecientes = array_slice($tareas, 0, 5);

// Porcentaje de tareas completadas
$porcentajeCompletadas = $totalTareas > 0
    ? round(($completadas / $totalTareas) * 100)
    : 0;

// Clases de estados
function claseEstado($estado)
{
    $clases = [
        "Pendiente" => "estado-pendiente",
        "En proceso" => "estado-proceso",
        "Por revisar" => "estado-revision",
        "Completada" => "estado-completada"
    ];

    return $clases[$estado] ?? "estado-default";
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content dashboard-empleado">

    <!-- ENCABEZADO -->

    <div class="dashboard-header">

        <div>
            <span class="dashboard-etiqueta">
                PANEL DE EMPLEADO
            </span>

            <h1>
                ¡Hola, <?= h($nombre) ?>!
            </h1>

            <p>
                Bienvenido a BUILDPRO. Este es el resumen de tus tareas
                y actividades asignadas.
            </p>
        </div>

        <div class="fecha-actual">
            <i class="fa-regular fa-calendar"></i>

            <?= date("d/m/Y") ?>
        </div>

    </div>


    <!-- ESTADÍSTICAS -->

    <section class="dashboard-stats">

        <article class="stat-card stat-total">

            <div class="stat-icon">
                <i class="fa-solid fa-list-check"></i>
            </div>

            <div>
                <span>Total de tareas</span>
                <h2><?= $totalTareas ?></h2>
                <small>Tareas asignadas</small>
            </div>

        </article>


        <article class="stat-card stat-pendiente">

            <div class="stat-icon">
                <i class="fa-regular fa-clock"></i>
            </div>

            <div>
                <span>Pendientes</span>
                <h2><?= $pendientes ?></h2>
                <small>Esperando comenzar</small>
            </div>

        </article>


        <article class="stat-card stat-proceso">

            <div class="stat-icon">
                <i class="fa-solid fa-spinner"></i>
            </div>

            <div>
                <span>En proceso</span>
                <h2><?= $enProceso ?></h2>
                <small>Trabajos en curso</small>
            </div>

        </article>


        <article class="stat-card stat-revision">

            <div class="stat-icon">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>

            <div>
                <span>Por revisar</span>
                <h2><?= $porRevisar ?></h2>
                <small>Enviadas al capataz</small>
            </div>

        </article>


        <article class="stat-card stat-completada">

            <div class="stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div>
                <span>Completadas</span>
                <h2><?= $completadas ?></h2>
                <small>Trabajos finalizados</small>
            </div>

        </article>


        <article class="stat-card stat-vencida">

            <div class="stat-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div>
                <span>Vencidas</span>
                <h2><?= $vencidas ?></h2>
                <small>Requieren atención</small>
            </div>

        </article>

    </section>


    <!-- CONTENIDO PRINCIPAL -->

    <section class="dashboard-grid">

        <!-- PROGRESO -->

        <article class="dashboard-panel progreso-panel">

            <div class="panel-header">
                <div>
                    <h2>Mi progreso</h2>
                    <p>Estado general de tus tareas.</p>
                </div>

                <i class="fa-solid fa-chart-pie"></i>
            </div>

            <div class="progreso-contenido">

                <div class="progreso-circulo"
                    style="--progreso: <?= $porcentajeCompletadas ?>%;">

                    <div class="progreso-centro">
                        <strong><?= $porcentajeCompletadas ?>%</strong>
                        <span>Completado</span>
                    </div>

                </div>

                <div class="progreso-detalles">

                    <div>
                        <span class="punto punto-completada"></span>
                        Completadas
                        <strong><?= $completadas ?></strong>
                    </div>

                    <div>
                        <span class="punto punto-proceso"></span>
                        En proceso
                        <strong><?= $enProceso ?></strong>
                    </div>

                    <div>
                        <span class="punto punto-pendiente"></span>
                        Pendientes
                        <strong><?= $pendientes ?></strong>
                    </div>

                    <div>
                        <span class="punto punto-revision"></span>
                        Por revisar
                        <strong><?= $porRevisar ?></strong>
                    </div>

                </div>

            </div>

        </article>


        <!-- PRÓXIMAS TAREAS -->

        <article class="dashboard-panel proximas-panel">

            <div class="panel-header">
                <div>
                    <h2>Próximas fechas límite</h2>
                    <p>Organizá tus próximos trabajos.</p>
                </div>

                <i class="fa-regular fa-calendar-days"></i>
            </div>

            <?php if (empty($proximasTareas)): ?>

                <div class="dashboard-vacio">
                    <i class="fa-regular fa-calendar-check"></i>

                    <p>
                        No tenés tareas pendientes con fecha límite próxima.
                    </p>
                </div>

            <?php else: ?>

                <div class="proximas-lista">

                    <?php foreach ($proximasTareas as $tarea): ?>

                        <div class="proxima-item">

                            <div class="proxima-icono">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>

                            <div class="proxima-info">

                                <strong>
                                    <?= h($tarea["titulo"] ?? "Sin título") ?>
                                </strong>

                                <span>
                                    <?= h($tarea["nombre_obra"] ?? "Sin obra") ?>
                                </span>

                            </div>

                            <div class="proxima-fecha">
                                <?= date(
                                    "d/m/Y",
                                    strtotime($tarea["fecha_limite"])
                                ) ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </article>

    </section>


    <!-- LISTADO DE TAREAS -->

    <section class="dashboard-panel tareas-panel">

        <div class="panel-header">

            <div>
                <h2>Mis tareas</h2>
                <p>Resumen de tus trabajos asignados.</p>
            </div>

            <span class="total-etiqueta">
                <?= $totalTareas ?> registros
            </span>

        </div>

        <?php if (empty($tareasRecientes)): ?>

            <div class="dashboard-vacio tareas-vacias">

                <i class="fa-solid fa-clipboard-list"></i>

                <h3>No tenés tareas asignadas</h3>

                <p>
                    Cuando te asignen un trabajo, aparecerá en este apartado.
                </p>

            </div>

        <?php else: ?>

            <div class="tabla-dashboard">

                <table>

                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Obra</th>
                            <th>Prioridad</th>
                            <th>Fecha límite</th>
                            <th>Estado</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($tareasRecientes as $tarea): ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= h($tarea["titulo"] ?? "Sin título") ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= h($tarea["nombre_obra"] ?? "Sin obra") ?>
                                </td>

                                <td>
                                    <span class="prioridad">
                                        <?= h($tarea["prioridad"] ?? "Sin prioridad") ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($tarea["fecha_limite"])): ?>

                                        <?= date(
                                            "d/m/Y",
                                            strtotime($tarea["fecha_limite"])
                                        ) ?>

                                    <?php else: ?>

                                        <span class="sin-fecha">
                                            Sin límite
                                        </span>

                                    <?php endif; ?>
                                </td>

                                <td>

                                    <?php
                                    $estado = $tarea["estado_asignacion"] ?? "";
                                    ?>

                                    <span class="estado-badge <?= claseEstado($estado) ?>">
                                        <?= h($estado) ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>


    <!-- AVISO INFORMATIVO -->

    <section class="dashboard-aviso">

        <div class="aviso-icono">
            <i class="fa-solid fa-circle-info"></i>
        </div>

        <div>
            <strong>Recordatorio</strong>

            <p>
                Mantené actualizado el progreso de tus trabajos.
                Cuando termines una tarea, informalo para que el capataz
                pueda revisarla y confirmar su finalización.
            </p>
        </div>

    </section>

</main>


<style>

/* =====================================
   DASHBOARD EMPLEADO - BUILDPRO
===================================== */

.dashboard-empleado {
    padding-bottom: 40px;
    color: #e2e8f0;
}

/* ENCABEZADO */

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
}

.dashboard-etiqueta {
    display: inline-block;
    color: #f4b400;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
    margin-bottom: 9px;
}

.dashboard-header h1 {
    color: #f8fafc;
    font-size: 29px;
    font-weight: 700;
    margin: 0 0 8px;
}

.dashboard-header p {
    color: #94a3b8;
    font-size: 14px;
    margin: 0;
}

.fecha-actual {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    background: #111c2a;
    border: 1px solid rgba(255,255,255,.07);
    color: #cbd5e1;
    font-size: 13px;
    white-space: nowrap;
}

.fecha-actual i {
    color: #f4b400;
}

/* ESTADÍSTICAS */

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 22px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 21px;
    background: #111c2a;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 16px;
    min-width: 0;
    transition: .2s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    border-color: rgba(244,180,0,.25);
}

.stat-icon {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 13px;
    font-size: 19px;
}

.stat-card span {
    display: block;
    color: #94a3b8;
    font-size: 12px;
    margin-bottom: 5px;
}

.stat-card h2 {
    color: #f8fafc;
    font-size: 25px;
    margin: 0;
    line-height: 1.2;
}

.stat-card small {
    display: block;
    color: #64748b;
    font-size: 11px;
    margin-top: 5px;
}

.stat-total .stat-icon {
    background: rgba(244,180,0,.13);
    color: #f4b400;
}

.stat-pendiente .stat-icon {
    background: rgba(245,158,11,.13);
    color: #fbbf24;
}

.stat-proceso .stat-icon {
    background: rgba(59,130,246,.13);
    color: #60a5fa;
}

.stat-revision .stat-icon {
    background: rgba(168,85,247,.13);
    color: #c084fc;
}

.stat-completada .stat-icon {
    background: rgba(34,197,94,.13);
    color: #4ade80;
}

.stat-vencida .stat-icon {
    background: rgba(239,68,68,.13);
    color: #f87171;
}

/* PANELES */

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 20px;
    margin-bottom: 22px;
}

.dashboard-panel {
    background: #111c2a;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 17px;
    padding: 23px;
    min-width: 0;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 22px;
}

.panel-header h2 {
    color: #f8fafc;
    font-size: 17px;
    margin: 0 0 6px;
}

.panel-header p {
    color: #8190a5;
    font-size: 12px;
    margin: 0;
}

.panel-header > i {
    color: #f4b400;
    font-size: 20px;
}

/* PROGRESO */

.progreso-contenido {
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 25px;
    padding: 10px 0;
}

.progreso-circulo {
    width: 145px;
    height: 145px;
    flex-shrink: 0;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: conic-gradient(
        #f4b400 var(--progreso),
        #26364a var(--progreso)
    );
    position: relative;
}

.progreso-circulo::before {
    content: "";
    position: absolute;
    width: 115px;
    height: 115px;
    border-radius: 50%;
    background: #111c2a;
}

.progreso-centro {
    position: relative;
    z-index: 1;
    text-align: center;
}

.progreso-centro strong {
    display: block;
    color: #f8fafc;
    font-size: 27px;
}

.progreso-centro span {
    display: block;
    color: #94a3b8;
    font-size: 11px;
}

.progreso-detalles {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
}

.progreso-detalles div {
    display: grid;
    grid-template-columns: 9px 1fr auto;
    align-items: center;
    gap: 9px;
    color: #aab8ca;
    font-size: 12px;
}

.progreso-detalles strong {
    color: #f8fafc;
    font-size: 13px;
}

.punto {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.punto-completada {
    background: #4ade80;
}

.punto-proceso {
    background: #60a5fa;
}

.punto-pendiente {
    background: #fbbf24;
}

.punto-revision {
    background: #c084fc;
}

/* PRÓXIMAS TAREAS */

.proximas-lista {
    display: flex;
    flex-direction: column;
}

.proxima-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 13px 0;
    border-bottom: 1px solid rgba(255,255,255,.06);
}

.proxima-item:last-child {
    border-bottom: none;
}

.proxima-icono {
    width: 39px;
    height: 39px;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 11px;
    background: rgba(244,180,0,.12);
    color: #f4b400;
}

.proxima-info {
    flex: 1;
    min-width: 0;
}

.proxima-info strong {
    display: block;
    color: #e2e8f0;
    font-size: 13px;
    overflow-wrap: anywhere;
}

.proxima-info span {
    display: block;
    color: #8190a5;
    font-size: 11px;
    margin-top: 4px;
}

.proxima-fecha {
    color: #f4b400;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

/* TABLA */

.tareas-panel {
    margin-bottom: 22px;
}

.total-etiqueta {
    padding: 7px 12px;
    border-radius: 20px;
    background: rgba(244,180,0,.12);
    color: #f4b400;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.tabla-dashboard {
    overflow-x: auto;
}

.tabla-dashboard table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

.tabla-dashboard th {
    padding: 14px 13px;
    text-align: left;
    color: #94a3b8;
    background: #172638;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
}

.tabla-dashboard th:first-child {
    border-radius: 9px 0 0 9px;
}

.tabla-dashboard th:last-child {
    border-radius: 0 9px 9px 0;
}

.tabla-dashboard td {
    padding: 16px 13px;
    color: #aab8ca;
    font-size: 12px;
    border-bottom: 1px solid rgba(255,255,255,.06);
}

.tabla-dashboard tbody tr:last-child td {
    border-bottom: none;
}

.tabla-dashboard tbody tr:hover {
    background: rgba(255,255,255,.025);
}

.tabla-dashboard td strong {
    color: #e2e8f0;
    font-weight: 600;
}

.prioridad {
    color: #cbd5e1;
}

.sin-fecha {
    color: #64748b;
    font-style: italic;
}

/* ESTADOS */

.estado-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

.estado-pendiente {
    background: rgba(245,158,11,.13);
    color: #fbbf24;
}

.estado-proceso {
    background: rgba(59,130,246,.13);
    color: #60a5fa;
}

.estado-revision {
    background: rgba(168,85,247,.13);
    color: #c084fc;
}

.estado-completada {
    background: rgba(34,197,94,.13);
    color: #4ade80;
}

.estado-default {
    background: rgba(148,163,184,.13);
    color: #cbd5e1;
}

/* VACÍOS */

.dashboard-vacio {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 30px 15px;
    color: #8190a5;
}

.dashboard-vacio > i {
    font-size: 29px;
    color: #f4b400;
    margin-bottom: 13px;
}

.dashboard-vacio p {
    font-size: 12px;
    line-height: 1.6;
    margin: 0;
}

.tareas-vacias h3 {
    color: #e2e8f0;
    font-size: 16px;
    margin: 0 0 7px;
}

/* AVISO */

.dashboard-aviso {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 18px 20px;
    border-radius: 14px;
    background: rgba(244,180,0,.07);
    border: 1px solid rgba(244,180,0,.18);
}

.aviso-icono {
    color: #f4b400;
    font-size: 20px;
    padding-top: 2px;
}

.dashboard-aviso strong {
    display: block;
    color: #f4b400;
    font-size: 13px;
    margin-bottom: 5px;
}

.dashboard-aviso p {
    color: #aab8ca;
    font-size: 12px;
    line-height: 1.6;
    margin: 0;
}

/* RESPONSIVE */

@media (max-width: 1100px) {

    .dashboard-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 650px) {

    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dashboard-header h1 {
        font-size: 24px;
    }

    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 17px;
    }

    .dashboard-panel {
        padding: 17px;
    }

    .progreso-contenido {
        flex-direction: column;
    }

    .progreso-detalles {
        max-width: 250px;
    }

    .proxima-item {
        flex-wrap: wrap;
    }

    .proxima-fecha {
        margin-left: 52px;
    }

}

</style>


<?php require_once "../../layouts/footer.php"; ?>
