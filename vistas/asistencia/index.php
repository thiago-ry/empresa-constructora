<?php

session_start();

require_once "../../modelos/Asistencia.php";
require_once "../../config/permisos.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../login/");
    exit;
}

verificarPermiso("empleados");

$usuario = $_SESSION["usuario"];

$id_usuario_empleado = (int)($_GET["id_usuario"] ?? 0);
$id_obra = (int)($_GET["id_obra"] ?? 0);

if ($id_usuario_empleado <= 0 || $id_obra <= 0) {
    header("Location: ../empleado_obra/");
    exit;
}

$asistenciaModel = new Asistencia();

$id_usuario_capataz = (int)($usuario["id"] ?? 0);

if ($id_usuario_capataz <= 0) {
    header("Location: ../dashboard/capataz.php");
    exit;
}

$empleado = $asistenciaModel->empleadoPerteneceObraCapataz(
    $id_usuario_empleado,
    $id_usuario_capataz,
    $id_obra
);

if (!$empleado) {
    header("Location: ../empleado_obra/?error=acceso");
    exit;
}

$fecha = date("Y-m-d");

$registro = $asistenciaModel->obtenerPorUsuarioFecha(
    $id_usuario_empleado,
    $id_obra,
    $fecha
);

$mensaje = $_GET["mensaje"] ?? "";
$error = $_GET["error"] ?? "";

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-header">

        <div>
            <span class="page-kicker">CONTROL DE PERSONAL</span>

            <h1>Asistencia</h1>

            <p>Registro de entrada y salida del empleado.</p>
        </div>

        <a
            href="../empleado_obra/?id_obra=<?= $id_obra ?>"
            class="btn-secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>

    </div>

    <?php if ($mensaje === "entrada"): ?>

        <div class="alert success">

            <i class="fa-solid fa-circle-check"></i>

            Entrada registrada correctamente.

        </div>

    <?php elseif ($mensaje === "salida"): ?>

        <div class="alert success">

            <i class="fa-solid fa-circle-check"></i>

            Salida registrada correctamente.

        </div>

    <?php elseif ($mensaje === "ausencia"): ?>

        <div class="alert success">

            <i class="fa-solid fa-circle-check"></i>

            Ausencia registrada correctamente.

        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="asistencia-grid">

        <div class="employee-card">

            <div class="employee-icon">

                <i class="fa-solid fa-user"></i>

            </div>

            <div>

                <span class="label">Empleado</span>

                <h2>
                    <?= htmlspecialchars(
                        $empleado["nombre"] . " " . $empleado["apellido"]
                    ) ?>
                </h2>

                <p>

                    <i class="fa-solid fa-briefcase"></i>

                    <?= htmlspecialchars(
                        $empleado["nombre_cargo"] ?? "Sin cargo"
                    ) ?>

                </p>

                <p>

                    <i class="fa-solid fa-building"></i>

                    <?= htmlspecialchars(
                        $empleado["nombre_obra"]
                    ) ?>

                </p>

            </div>

        </div>


        <div class="attendance-card">

            <div class="card-header">

                <div>

                    <span class="label">HOY</span>

                    <h2>
                        <?= date("d/m/Y") ?>
                    </h2>

                </div>

                <div class="calendar-icon">

                    <i class="fa-solid fa-calendar-day"></i>

                </div>

            </div>


            <?php if ($registro && $registro["estado"] === "Ausente"): ?>

                <div
                    style="
                        margin-bottom: 20px;
                        padding: 14px 16px;
                        border-radius: 12px;
                        background: rgba(239, 68, 68, .10);
                        border: 1px solid rgba(239, 68, 68, .25);
                        color: #f87171;
                        font-weight: 700;
                        text-align: center;
                    "
                >

                    <i class="fa-solid fa-circle-xmark"></i>

                    Empleado marcado como ausente

                </div>

            <?php endif; ?>


            <div class="attendance-times">

                <div class="time-box">

                    <span>Entrada</span>

                    <strong>

                        <?php

                        if (
                            $registro &&
                            !empty($registro["hora_entrada"])
                        ) {

                            echo date(
                                "H:i",
                                strtotime($registro["hora_entrada"])
                            );

                        } else {

                            echo "--:--";

                        }

                        ?>

                    </strong>

                </div>


                <div class="time-box">

                    <span>Salida</span>

                    <strong>

                        <?php

                        if (
                            $registro &&
                            !empty($registro["hora_salida"])
                        ) {

                            echo date(
                                "H:i",
                                strtotime($registro["hora_salida"])
                            );

                        } else {

                            echo "--:--";

                        }

                        ?>

                    </strong>

                </div>

            </div>


            <div class="attendance-actions">

                <?php if (!$registro): ?>

                    <a
                        href="../../controladores/AsistenciaController.php?accion=entrada&id_usuario=<?= $id_usuario_empleado ?>&id_obra=<?= $id_obra ?>"
                        class="btn-entry"
                    >

                        <i class="fa-solid fa-right-to-bracket"></i>

                        Marcar entrada

                    </a>


                    <a
                        href="../../controladores/AsistenciaController.php?accion=ausencia&id_usuario=<?= $id_usuario_empleado ?>&id_obra=<?= $id_obra ?>"
                        class="btn-exit"
                        style="margin-top: 10px; background: #dc3545;"
                        onclick="return confirm('¿Marcar a este empleado como ausente?');"
                    >

                        <i class="fa-solid fa-user-xmark"></i>

                        Marcar ausencia

                    </a>


                <?php elseif ($registro["estado"] === "Ausente"): ?>

                    <a
                        href="../../controladores/AsistenciaController.php?accion=entrada&id_usuario=<?= $id_usuario_empleado ?>&id_obra=<?= $id_obra ?>"
                        class="btn-entry"
                        onclick="return confirm('¿El empleado llegó a la obra? Se registrará su entrada.');"
                    >

                        <i class="fa-solid fa-right-to-bracket"></i>

                        Registrar entrada

                    </a>


                <?php elseif (empty($registro["hora_salida"])): ?>

                    <a
                        href="../../controladores/AsistenciaController.php?accion=salida&id_usuario=<?= $id_usuario_empleado ?>&id_obra=<?= $id_obra ?>"
                        class="btn-exit"
                        onclick="return confirm('¿Registrar la salida de este empleado?');"
                    >

                        <i class="fa-solid fa-right-from-bracket"></i>

                        Marcar salida

                    </a>


                <?php else: ?>

                    <div class="completed">

                        <i class="fa-solid fa-circle-check"></i>

                        Jornada registrada

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>


<style>

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

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 16px;
    background: #111c2a;
    color: #fff;
    text-decoration: none;
    border: 1px solid #263548;
    border-radius: 10px;
    transition: .2s;
}

.btn-secondary:hover {
    border-color: #f4b400;
    color: #f4b400;
}

.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert.success {
    background: rgba(34, 197, 94, .12);
    border: 1px solid rgba(34, 197, 94, .3);
    color: #4ade80;
}

.alert.error {
    background: rgba(239, 68, 68, .12);
    border: 1px solid rgba(239, 68, 68, .3);
    color: #f87171;
}

.asistencia-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 22px;
}

.employee-card,
.attendance-card {
    background: #111c2a;
    border: 1px solid #1f2d3d;
    border-radius: 18px;
    padding: 25px;
}

.employee-card {
    display: flex;
    align-items: flex-start;
    gap: 18px;
}

.employee-icon {
    width: 55px;
    height: 55px;
    min-width: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 180, 0, .12);
    color: #f4b400;
    font-size: 22px;
}

.label {
    display: block;
    color: #7f8c9d;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 6px;
}

.employee-card h2 {
    margin: 0 0 15px;
    color: #fff;
    font-size: 23px;
}

.employee-card p {
    margin: 9px 0;
    color: #aeb9c7;
}

.employee-card p i {
    width: 20px;
    color: #f4b400;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
}

.card-header h2 {
    margin: 0;
    color: #fff;
    font-size: 25px;
}

.calendar-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 180, 0, .12);
    color: #f4b400;
    font-size: 20px;
}

.attendance-times {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.time-box {
    padding: 20px;
    border-radius: 14px;
    background: #0b1320;
    border: 1px solid #1f2d3d;
    text-align: center;
}

.time-box span {
    display: block;
    color: #7f8c9d;
    font-size: 13px;
    margin-bottom: 8px;
}

.time-box strong {
    display: block;
    color: #fff;
    font-size: 28px;
}

.attendance-actions {
    margin-top: 22px;
}

.btn-entry,
.btn-exit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: .2s;
}

.btn-entry {
    background: #f4b400;
    color: #111;
}

.btn-entry:hover {
    background: #ffc928;
}

.btn-exit {
    background: #dc3545;
    color: #fff;
}

.btn-exit:hover {
    background: #ef4444;
}

.completed {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 14px;
    border-radius: 12px;
    background: rgba(34, 197, 94, .1);
    border: 1px solid rgba(34, 197, 94, .25);
    color: #4ade80;
    font-weight: 700;
}

@media (max-width: 850px) {

    .asistencia-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 600px) {

    .page-header {
        flex-direction: column;
        align-items: stretch;
    }

    .page-header h1 {
        font-size: 25px;
    }

    .attendance-times {
        grid-template-columns: 1fr;
    }

}

</style>