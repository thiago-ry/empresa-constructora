<?php

session_start();

require_once "../../modelos/Tarea.php";
require_once "../../modelos/Obra.php";

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

$tareaModelo = new Tarea();
$obraModelo = new Obra();

$obras = $obraModelo->obtenerObrasVisibles($usuario);
$tareas = $tareaModelo->listarCapataz($id_usuario);

if (!is_array($obras)) {
    $obras = [];
}

if (!is_array($tareas)) {
    $tareas = [];
}

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
            <h1>Gestión de tareas</h1>
            <p>Administrá los trabajos y revisá el avance de tus obras.</p>
        </div>
    </div>

    <?php if (!empty($_GET["mensaje"])): ?>
        <div class="alert alert-info">
            <?= h($_GET["mensaje"]) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Crear una tarea</h2>
        <p>Seleccioná la obra desde la que vas a organizar el trabajo.</p>

        <?php if (empty($obras)): ?>
            <p>No tenés obras asignadas para gestionar tareas.</p>
        <?php else: ?>
            <div class="task-list">
                <?php foreach ($obras as $obra): ?>
                    <article class="card task-card">
                        <div class="task-card-header">
                            <div>
                                <h3><?= h($obra["nombre_obra"]) ?></h3>
                            </div>

                            <a
                                class="btn btn-primary"
                                href="crear.php?id_obra=<?= (int) $obra["id_obra"] ?>"
                            >
                                Crear tarea
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Tareas registradas</h2>

        <?php if (empty($tareas)): ?>
            <p>Todavía no hay tareas registradas en tus obras.</p>
        <?php else: ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Tarea</th>
                            <th>Prioridad</th>
                            <th>Fecha límite</th>
                            <th>Por revisar</th>
                            <th>Completadas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($tareas as $fila): ?>
                            <tr>
                                <td><?= h($fila["nombre_obra"]) ?></td>
                                <td><?= h($fila["titulo"]) ?></td>
                                <td><?= h($fila["prioridad"]) ?></td>
                                <td>
                                    <?= h(
                                        $fila["fecha_limite"]
                                        ?: "Sin límite"
                                    ) ?>
                                </td>
                                <td><?= (int) $fila["por_revisar"] ?></td>
                                <td><?= (int) $fila["completadas"] ?></td>
                                <td>
                                    <a
                                        class="btn btn-primary"
                                        href="revisar.php?id=<?= (int) $fila["id_tarea"] ?>"
                                    >
                                        Revisar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

</main>

<?php require_once "../../layouts/footer.php"; ?>