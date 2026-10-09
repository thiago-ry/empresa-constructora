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

if ($id_rol !== 7 || $id_usuario <= 0) {
    http_response_code(403);
    exit("Acceso denegado.");
}

$id_tarea = (int) ($_GET["id"] ?? 0);

if ($id_tarea <= 0) {
    header("Location: index.php");
    exit;
}

$tareaModelo = new Tarea();
$asignaciones = $tareaModelo->obtenerAsignaciones(
    $id_tarea,
    $id_usuario
);

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
            <h1>Revisión de tareas</h1>
            <p>Controlá el cumplimiento del trabajo de cada empleado.</p>
        </div>

        <a href="index.php" class="btn btn-secondary">
            Volver
        </a>
    </div>

    <?php if (!empty($_GET["mensaje"])): ?>
        <div class="alert alert-info">
            <?= h($_GET["mensaje"]) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($asignaciones)): ?>

        <div class="card">
            <h2>Sin asignaciones</h2>
            <p>
                Esta tarea no tiene asignaciones o no pertenece
                a una de tus obras.
            </p>
        </div>

    <?php else: ?>

        <?php foreach ($asignaciones as $fila): ?>

            <article class="card task-card">

                <div class="task-card-header">
                    <h2>
                        <?= h(
                            $fila["apellido"] . ", " . $fila["nombre"]
                        ) ?>
                    </h2>

                    <span class="badge">
                        <?= h($fila["estado"]) ?>
                    </span>
                </div>

                <p>
                    <strong>Fecha de inicio:</strong>
                    <?= h($fila["fecha_inicio"] ?: "Sin iniciar") ?>
                </p>

                <p>
                    <strong>Fecha de finalización:</strong>
                    <?= h(
                        $fila["fecha_finalizacion"] ?: "Sin finalizar"
                    ) ?>
                </p>

                <div class="form-group">
                    <strong>Observación del empleado:</strong>
                    <p>
                        <?= nl2br(h(
                            $fila["observacion_empleado"]
                            ?: "Sin observaciones."
                        )) ?>
                    </p>
                </div>

                <?php if (!empty($fila["observacion_capataz"])): ?>
                    <div class="form-group">
                        <strong>Observación del capataz:</strong>
                        <p>
                            <?= nl2br(h($fila["observacion_capataz"])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($fila["estado"] === "Por revisar"): ?>

                    <form
                        method="POST"
                        action="../../controladores/TareaController.php?accion=revisar"
                    >
                        <input
                            type="hidden"
                            name="id_tarea_empleado"
                            value="<?= (int) $fila["id_tarea_empleado"] ?>"
                        >

                        <div class="form-group">
                            <label
                                for="observacion_<?= (int) $fila["id_tarea_empleado"] ?>"
                            >
                                Observación de la revisión
                            </label>

                            <textarea
                                class="form-control"
                                id="observacion_<?= (int) $fila["id_tarea_empleado"] ?>"
                                name="observacion_capataz"
                                rows="3"
                                maxlength="2000"
                                placeholder="Indicá si el trabajo está bien o qué falta."
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            name="estado"
                            value="Completada"
                        >
                            Confirmar cumplimiento
                        </button>

                        <button
                            type="submit"
                            class="btn btn-secondary"
                            name="estado"
                            value="En proceso"
                        >
                            Devolver para continuar
                        </button>
                    </form>

                <?php elseif ($fila["estado"] === "Completada"): ?>

                    <p>El cumplimiento fue confirmado.</p>

                <?php elseif ($fila["estado"] === "En proceso"): ?>

                    <p>El empleado debe continuar con el trabajo.</p>

                <?php elseif ($fila["estado"] === "Pendiente"): ?>

                    <p>El empleado todavía no inició la tarea.</p>

                <?php endif; ?>

            </article>

        <?php endforeach; ?>

    <?php endif; ?>

</main>

<?php require_once "../../layouts/footer.php"; ?>