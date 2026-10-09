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

                        <span class="badge">
                            <?= h($fila["estado_asignacion"]) ?>
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
                            action="../../controladores/TareaController.php?accion=actualizar_estado"
                        >
                            <input
                                type="hidden"
                                name="id_tarea_empleado"
                                value="<?= (int) $fila["id_tarea_empleado"] ?>"
                            >

                            <div class="form-group">
                                <label
                                    for="obs_<?= (int) $fila["id_tarea_empleado"] ?>"
                                >
                                    Observación del trabajo
                                </label>

                                <textarea
                                    class="form-control"
                                    id="obs_<?= (int) $fila["id_tarea_empleado"] ?>"
                                    name="observacion_empleado"
                                    rows="3"
                                    maxlength="2000"
                                    placeholder="Contá brevemente cómo va el trabajo."
                                ><?= h($fila["observacion_empleado"] ?? "") ?></textarea>
                            </div>

                            <input
                                type="hidden"
                                name="estado"
                                value="<?= $fila["estado_asignacion"] === "Pendiente"
                                    ? "En proceso"
                                    : "Por revisar" ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
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