<?php

session_start();

require_once "../../modelos/Tarea.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: /empresa_constructora/vistas/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];
$id_rol = (int) ($usuario["id_rol"] ?? 0);
$id_usuario = (int) (
    $usuario["id_usuario"] ?? $usuario["id"] ?? 0
);

if ($id_rol !== 1 || $id_usuario <= 0) {
    http_response_code(403);
    exit("Acceso denegado.");
}

$tareaModelo = new Tarea();
$tareas = $tareaModelo->listarEmpleado($id_usuario);

function h($valor)
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<style>
    /* ================================
   MIS TAREAS - BUILDPRO
================================ */

    .content {
        padding-bottom: 40px;
    }

    /* ENCABEZADO */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .page-header h1 {
        color: #f1f5f9;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .page-header p {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
    }

    /* MENSAJES */

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 22px;
        background: rgba(59, 130, 246, .12);
        border: 1px solid rgba(59, 130, 246, .25);
        color: #93c5fd;
    }

    /* ESTADO VACÍO */

    .card:not(.task-card) {
        background: #111c2a;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 18px;
        padding: 30px;
        color: #e2e8f0;
    }

    .card:not(.task-card) h2 {
        color: #f4b400;
        font-size: 21px;
        margin-top: 0;
    }

    .card:not(.task-card) p {
        color: #94a3b8;
        margin-bottom: 0;
    }

    /* LISTADO */

    .task-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
        gap: 20px;
        align-items: start;
    }

    /* TARJETA */

    .task-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(145deg, #142235, #0f1a29);
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 18px;
        padding: 23px;
        color: #cbd5e1;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .16);
        transition: transform .2s ease, border-color .2s ease,
            box-shadow .2s ease;
    }

    .task-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #f4b400;
    }

    .task-card:hover {
        transform: translateY(-4px);
        border-color: rgba(244, 180, 0, .3);
        box-shadow: 0 14px 32px rgba(0, 0, 0, .25);
    }

    /* CABECERA DE TARJETA */

    .task-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 18px;
    }

    .task-card-header div {
        min-width: 0;
    }

    .task-card-header span:not(.badge) {
        display: block;
        color: #f4b400;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 7px;
    }

    .task-card-header h2 {
        color: #f8fafc;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.35;
        margin: 0;
        overflow-wrap: anywhere;
    }

    /* BADGES */

    .task-card .badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(148, 163, 184, .15);
        color: #cbd5e1;
        border: 1px solid rgba(148, 163, 184, .2);
        white-space: nowrap;
    }

    /* COLORES SEGÚN ESTADO */

    .task-card .badge:has(+ *) {
        margin-bottom: 0;
    }

    /* INFORMACIÓN */

    .task-card>p {
        font-size: 14px;
        line-height: 1.6;
        color: #aab8ca;
        margin: 12px 0;
        overflow-wrap: anywhere;
    }

    .task-card>p strong {
        color: #e2e8f0;
        font-weight: 600;
    }

    /* SEPARACIÓN DEL FORMULARIO */

    .task-card form {
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, .08);
    }

    /* FORMULARIO */

    .task-card .form-group {
        margin-bottom: 17px;
    }

    .task-card label {
        display: block;
        color: #e2e8f0;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 9px;
    }

    .task-card textarea,
    .task-card .form-control {
        width: 100%;
        min-height: 95px;
        resize: vertical;
        padding: 13px 15px;
        border-radius: 11px;
        border: 1px solid #2b3c51;
        background: #0b1320;
        color: #f8fafc;
        font-family: inherit;
        font-size: 13px;
        line-height: 1.5;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .task-card textarea::placeholder {
        color: #718096;
    }

    .task-card textarea:focus {
        border-color: #f4b400;
        box-shadow: 0 0 0 3px rgba(244, 180, 0, .1);
    }

    /* BOTONES */

    .task-card .btn {
        width: 100%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 12px 18px;
        border: none;
        border-radius: 11px;
        background: #f4b400;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s, transform .2s;
    }

    .task-card .btn:hover {
        background: #ffc928;
        transform: translateY(-1px);
    }

    .task-card .btn:active {
        transform: scale(.98);
    }

    /* MENSAJES DE TAREA */

    .task-card>p:last-child {
        margin-bottom: 0;
    }

    /* RESPONSIVE */

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 24px;
        }

        .task-list {
            grid-template-columns: 1fr;
        }

        .task-card {
            padding: 19px;
        }
    }

    @media (max-width: 480px) {
        .task-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .task-card-header h2 {
            font-size: 17px;
        }
    }
    .estado-pendiente {
    background: rgba(245, 158, 11, .13) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, .25) !important;
}

.estado-proceso {
    background: rgba(59, 130, 246, .13) !important;
    color: #60a5fa !important;
    border-color: rgba(59, 130, 246, .25) !important;
}

.estado-revision {
    background: rgba(168, 85, 247, .13) !important;
    color: #c084fc !important;
    border-color: rgba(168, 85, 247, .25) !important;
}

.estado-completada {
    background: rgba(34, 197, 94, .13) !important;
    color: #4ade80 !important;
    border-color: rgba(34, 197, 94, .25) !important;
}
</style>

<main class="content">

    <div class="page-header">
        <div>
            <h1>Mis tareas</h1>
            <p>Consultá tus trabajos y actualizá su progreso.</p>
        </div>
    </div>

    <?php if (!empty($_GET["mensaje"])): ?>
        <div class="alert alert-info">
            <?= h($_GET["mensaje"]) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($tareas)): ?>

        <div class="card">
            <h2>No tenés tareas asignadas</h2>
            <p>Cuando te asignen un trabajo, aparecerá acá.</p>
        </div>

    <?php else: ?>

        <div class="task-list">

            <?php foreach ($tareas as $fila): ?>

                <article class="card task-card">

                    <div class="task-card-header">
                        <div>
                            <span><?= h($fila["nombre_obra"]) ?></span>
                            <h2><?= h($fila["titulo"]) ?></h2>
                        </div>

                        <?php
                        $estado = $fila["estado_asignacion"];

                        $clasesEstado = [
                            "Pendiente" => "estado-pendiente",
                            "En proceso" => "estado-proceso",
                            "Por revisar" => "estado-revision",
                            "Completada" => "estado-completada"
                        ];

                        $claseEstado = $clasesEstado[$estado] ?? "estado-default";
                        ?>

                        <span class="badge <?= $claseEstado ?>">
                            <?= h($estado) ?>
                        </span>
                    </div>

                    <p>
                        <?= nl2br(h($fila["descripcion"])) ?>
                    </p>

                    <p>
                        <strong>Prioridad:</strong>
                        <?= h($fila["prioridad"]) ?>
                    </p>

                    <p>
                        <strong>Fecha límite:</strong>
                        <?= h($fila["fecha_limite"] ?: "Sin límite") ?>
                    </p>

                    <?php if (!empty($fila["observacion_capataz"])): ?>
                        <p>
                            <strong>Observación del capataz:</strong>
                            <?= nl2br(h($fila["observacion_capataz"])) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (
                        in_array(
                            $fila["estado_asignacion"],
                            ["Pendiente", "En proceso"],
                            true
                        )
                    ): ?>

                        <form
                            method="POST"
                            action="../../controladores/TareaController.php?accion=actualizar_estado">
                            <input
                                type="hidden"
                                name="id_tarea_empleado"
                                value="<?= (int) $fila["id_tarea_empleado"] ?>">

                            <div class="form-group">
                                <label
                                    for="obs_<?= (int) $fila["id_tarea_empleado"] ?>">
                                    Observación del trabajo
                                </label>

                                <textarea
                                    class="form-control"
                                    id="obs_<?= (int) $fila["id_tarea_empleado"] ?>"
                                    name="observacion_empleado"
                                    rows="3"
                                    maxlength="2000"
                                    placeholder="Contá brevemente cómo va el trabajo."><?= h($fila["observacion_empleado"] ?? "") ?></textarea>
                            </div>

                            <input
                                type="hidden"
                                name="estado"
                                value="<?= $fila["estado_asignacion"] === "Pendiente"
                                            ? "En proceso"
                                            : "Por revisar" ?>">

                            <button
                                type="submit"
                                class="btn btn-primary">
                                <?= $fila["estado_asignacion"] === "Pendiente"
                                    ? "Comenzar tarea"
                                    : "Informar que terminé" ?>
                            </button>
                        </form>

                    <?php elseif (
                        $fila["estado_asignacion"] === "Por revisar"
                    ): ?>

                        <p>
                            Tu tarea fue enviada al capataz para su revisión.
                        </p>

                    <?php elseif (
                        $fila["estado_asignacion"] === "Completada"
                    ): ?>

                        <p>Esta tarea fue confirmada como completada.</p>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

<?php require_once "../../layouts/footer.php"; ?>