<?php

require_once __DIR__ . "/../../../modelos/EmpleadoObra.php";
require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


/*
==================================================
    OBTENER ID
==================================================
*/

$id = $_GET["id"] ?? 0;

if (empty($id)) {

    header(
        "Location: ../../../controladores/EmpleadoObraController.php?accion=listar"
    );

    exit();
}


/*
==================================================
    OBTENER ASIGNACIÓN
==================================================
*/

$empleadoObra = new EmpleadoObra();

$empleado = $empleadoObra->buscarPorId($id);


if (!$empleado) {

    echo "No se encontró la asignación del empleado.";

    exit();
}


/*
==================================================
    OBTENER CARGOS DEL EMPLEADO
==================================================
*/

$cargos = $empleadoObra->obtenerCargosEmpleado(
    $empleado["id_usuario"]
);


/*
==================================================
    DATOS
==================================================
*/

$id_obra = $empleado["id_obra"];

$id_cargo_actual =
    $empleado["id_cargo"] ?? "";

$fecha_ingreso =
    $empleado["fecha_ingreso"] ?? "";

$observaciones =
    $empleado["observaciones"] ?? "";


/*
==================================================
    LAYOUT
==================================================
*/

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">


    <!-- ==================================================
         ENCABEZADO
    ================================================== -->

    <div class="page-title">

        <h1>
            Editar asignación
        </h1>

        <p>
            Modifique los datos del empleado asignado a la obra.
        </p>

    </div>



    <!-- ==================================================
         FORMULARIO
    ================================================== -->

    <div class="form-card">


        <form
            action="/empresa_constructora/controladores/EmpleadoObraController.php"
            method="POST"
            class="form"
        >


            <!-- ==================================================
                 ACCIÓN
            ================================================== -->

            <input
                type="hidden"
                name="accion"
                value="editar"
            >


            <!-- ==================================================
                 ID ASIGNACIÓN
            ================================================== -->

            <input
                type="hidden"
                name="id_empleado_obra"
                value="<?= htmlspecialchars($empleado["id_empleado_obra"]) ?>"
            >


            <!-- ==================================================
                 ID OBRA
            ================================================== -->

            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($id_obra) ?>"
            >


            <!-- ==================================================
                 EMPLEADO
            ================================================== -->

            <div class="form-group">

                <label>

                    <i class="fa-solid fa-user"></i>

                    Empleado

                </label>


                <div
                    style="
                        padding:15px 18px;
                        border:1px solid rgba(255,255,255,.1);
                        border-radius:8px;
                    "
                >

                    <strong>

                        <?= htmlspecialchars(
                            $empleado["apellido"]
                            . ", "
                            . $empleado["nombre"]
                        ) ?>

                    </strong>


                    <br>


                    <small>

                        DNI:
                        <?= htmlspecialchars(
                            $empleado["documento"] ?? "-"
                        ) ?>

                        <?php if (!empty($empleado["telefono"])): ?>

                            &nbsp; | &nbsp;

                            Tel:
                            <?= htmlspecialchars(
                                $empleado["telefono"]
                            ) ?>

                        <?php endif; ?>

                    </small>

                </div>


                <small
                    style="
                        display:block;
                        margin-top:8px;
                    "
                >

                    El empleado no puede cambiarse desde esta pantalla.

                </small>

            </div>



            <!-- ==================================================
                 CARGO
            ================================================== -->

            <div class="form-group">

                <label for="id_cargo">

                    <i class="fa-solid fa-briefcase"></i>

                    Cargo en la obra

                </label>


                <select
                    name="id_cargo"
                    id="id_cargo"
                    class="input"
                    required
                >

                    <option value="">

                        Seleccione el cargo

                    </option>


                    <?php foreach ($cargos as $cargo): ?>


                        <option
                            value="<?= htmlspecialchars($cargo["id_cargo"]) ?>"
                            <?= (
                                $cargo["id_cargo"] == $id_cargo_actual
                                    ? "selected"
                                    : ""
                            ) ?>
                        >

                            <?= htmlspecialchars(
                                $cargo["nombre_cargo"]
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>


                <?php if (empty($cargos)): ?>

                    <small
                        style="
                            display:block;
                            margin-top:8px;
                        "
                    >

                        Este empleado no tiene cargos registrados.

                    </small>

                <?php else: ?>

                    <small
                        style="
                            display:block;
                            margin-top:8px;
                        "
                    >

                        Seleccione el cargo que desempeñará el empleado
                        en esta obra.

                    </small>

                <?php endif; ?>

            </div>



            <!-- ==================================================
                 FECHA DE INGRESO
            ================================================== -->

            <div class="form-group">

                <label for="fecha_ingreso">

                    <i class="fa-solid fa-calendar"></i>

                    Fecha de ingreso

                </label>


                <input
                    type="date"
                    name="fecha_ingreso"
                    id="fecha_ingreso"
                    class="input"
                    value="<?= htmlspecialchars($fecha_ingreso) ?>"
                    required
                >

            </div>



            <!-- ==================================================
                 OBSERVACIONES
            ================================================== -->

            <div class="form-group">

                <label for="observaciones">

                    <i class="fa-solid fa-note-sticky"></i>

                    Observaciones

                </label>


                <textarea
                    name="observaciones"
                    id="observaciones"
                    class="input"
                    rows="5"
                    placeholder="Ingrese observaciones sobre la asignación..."
                ><?= htmlspecialchars($observaciones) ?></textarea>

            </div>



            <!-- ==================================================
                 INFORMACIÓN DEL ESTADO
            ================================================== -->

            <div
                style="
                    padding:15px 18px;
                    margin-bottom:25px;
                    border-radius:8px;
                    border:1px solid rgba(255,255,255,.08);
                "
            >

                <strong>

                    <i class="fa-solid fa-circle-info"></i>

                    Estado de la asignación

                </strong>


                <p
                    style="
                        margin:8px 0 0;
                    "
                >

                    <?php if ((int)$empleado["estado"] === 1): ?>

                        El empleado se encuentra
                        <strong>activo</strong>
                        en esta obra.

                    <?php else: ?>

                        El empleado se encuentra
                        <strong>retirado</strong>
                        de esta obra.

                    <?php endif; ?>

                </p>

            </div>



            <!-- ==================================================
                 BOTONES
            ================================================== -->

            <div class="form-actions">


                <a
                    href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=listar&id_obra=<?= htmlspecialchars($id_obra) ?>"
                    class="btn btn-secondary"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>


                <button
                    type="reset"
                    class="btn btn-warning"
                >

                    <i class="fa-solid fa-rotate-left"></i>

                    Restablecer

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar cambios

                </button>


            </div>


        </form>


    </div>


</main>


<?php

require_once __DIR__ . "/../../../layouts/footer.php";

?>