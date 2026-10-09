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

$id_obra = (int) ($_GET["id_obra"] ?? 0);

if ($id_obra <= 0) {
    header("Location: index.php?mensaje=" . urlencode(
        "Primero ingresá a una obra."
    ));
    exit;
}

// Validar acceso utilizando el modelo Obra.
$obras = $obraModelo->obtenerObrasVisibles($usuario);
$obraActual = null;

if (is_array($obras)) {
    foreach ($obras as $obra) {
        if ((int) $obra["id_obra"] === $id_obra) {
            $obraActual = $obra;
            break;
        }
    }
}

// Verificación adicional de seguridad para el capataz.
if (
    !$obraActual ||
    !$tareaModelo->capatazTieneObra($id_usuario, $id_obra)
) {
    http_response_code(403);
    exit("No tenés acceso a esta obra.");
}

$empleados = $tareaModelo->obtenerEmpleadosPorObra($id_obra);

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
            <h1>Nueva tarea</h1>
            <p>
                Obra:
                <strong><?= h($obraActual["nombre_obra"]) ?></strong>
            </p>
        </div>

        <a
            href="index.php"
            class="btn btn-secondary"
        >
            Volver
        </a>
    </div>

    <?php if (!empty($_GET["mensaje"])): ?>
        <div class="alert alert-warning">
            <?= h($_GET["mensaje"]) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <form
            method="POST"
            action="../../controladores/TareaController.php?accion=crear"
        >

            <input
                type="hidden"
                name="id_obra"
                value="<?= $id_obra ?>"
            >

            <div class="form-group">
                <label for="titulo">Título de la tarea</label>

                <input
                    class="form-control"
                    type="text"
                    id="titulo"
                    name="titulo"
                    maxlength="150"
                    required
                >
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción del trabajo</label>

                <textarea
                    class="form-control"
                    id="descripcion"
                    name="descripcion"
                    rows="4"
                    required
                ></textarea>
            </div>

            <div class="form-group">
                <label for="prioridad">Prioridad</label>

                <select
                    class="form-control"
                    id="prioridad"
                    name="prioridad"
                    required
                >
                    <option value="Baja">Baja</option>
                    <option value="Media" selected>Media</option>
                    <option value="Alta">Alta</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_limite">Fecha límite</label>

                <input
                    class="form-control"
                    type="date"
                    id="fecha_limite"
                    name="fecha_limite"
                    min="<?= date("Y-m-d") ?>"
                >
            </div>

            <div class="form-group">
                <label>Empleados asignados</label>

                <?php if (empty($empleados)): ?>
                    <p>No hay empleados activos asignados a esta obra.</p>
                <?php else: ?>

                    <div class="checkbox-list">
                        <?php foreach ($empleados as $empleado): ?>
                            <label class="checkbox-item">
                                <input
                                    type="checkbox"
                                    name="empleados[]"
                                    value="<?= (int) $empleado["id_usuario"] ?>"
                                >

                                <?= h(
                                    $empleado["apellido"] . ", " .
                                    $empleado["nombre"]
                                ) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>
            </div>

            <button
                type="submit"
                class="btn btn-primary"
                <?= empty($empleados) ? "disabled" : "" ?>
            >
                Crear y asignar tarea
            </button>

        </form>
    </div>

</main>

<?php require_once "../../layouts/footer.php"; ?>