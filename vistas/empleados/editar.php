<?php

require_once "../../modelos/Usuario.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$usuarioModel = new Usuario();

$id = intval($_GET["id"]);

$empleado = $usuarioModel->buscarPorId($id);

if (!$empleado || $empleado["nombre_rol"] != "Empleado") {
    header("Location: index.php");
    exit;
}

$cargos = $usuarioModel->obtenerCargos();

$cargosEmpleado = $usuarioModel->obtenerIdsCargosEmpleado($id);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Editar empleado
        </h1>

        <p>
            Modifique los datos del empleado.
        </p>

    </div>


    <div class="form-card">

        <form
            class="form"
            action="../../controladores/EmpleadoController.php"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="accion"
                value="editar">

            <input
                type="hidden"
                name="id_usuario"
                value="<?= $empleado["id_usuario"]; ?>">


            <!-- ==========================
                 NOMBRE Y APELLIDO
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="100"
                        value="<?= htmlspecialchars($empleado["nombre"]); ?>"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Apellido
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        class="input"
                        maxlength="100"
                        value="<?= htmlspecialchars($empleado["apellido"]); ?>"
                        required>

                </div>

            </div>


            <!-- ==========================
                 CORREO Y ROL
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Correo
                    </label>

                    <input
                        type="email"
                        name="correo"
                        class="input"
                        maxlength="150"
                        value="<?= htmlspecialchars($empleado["correo"]); ?>"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Rol
                    </label>

                    <p class="input">
                        Empleado
                    </p>

                </div>

            </div>


            <!-- ==========================
                 DOCUMENTO Y TELÉFONO
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Documento
                    </label>

                    <input
                        type="text"
                        name="documento"
                        class="input"
                        maxlength="20"
                        value="<?= htmlspecialchars($empleado["documento"] ?? ""); ?>"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        class="input"
                        maxlength="30"
                        value="<?= htmlspecialchars($empleado["telefono"] ?? ""); ?>">

                </div>

            </div>


            <!-- ==========================
                 DIRECCIÓN Y SALARIO
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="input"
                        maxlength="200"
                        value="<?= htmlspecialchars($empleado["direccion"] ?? ""); ?>">

                </div>


                <div class="form-group">

                    <label>
                        Salario
                    </label>

                    <input
                        type="number"
                        name="salario"
                        class="input"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars($empleado["salario"] ?? ""); ?>"
                        required>

                </div>

            </div>


            <!-- ==========================
                 CARGOS
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Cargos
                    </label>

                    <select
                        name="cargos[]"
                        class="input"
                        multiple
                        required
                        style="height: 120px;">

                        <?php foreach ($cargos as $cargo) { ?>

                            <option
                                value="<?= $cargo["id_cargo"]; ?>"
                                <?= in_array(
                                    $cargo["id_cargo"],
                                    $cargosEmpleado
                                ) ? "selected" : ""; ?>>

                                <?= htmlspecialchars($cargo["nombre_cargo"]); ?>

                            </option>

                        <?php } ?>

                    </select>

                    <small>
                        Mantenga presionada la tecla
                        <strong>Ctrl</strong>
                        para seleccionar más de un cargo.
                    </small>

                </div>

            </div>


            <!-- ==========================
                 BOTONES
            =========================== -->

            <div class="form-actions">

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>


                <button
                    type="submit"
                    class="btn btn-warning">

                    <i class="fa-solid fa-pen"></i>

                    Guardar cambios

                </button>

            </div>

        </form>

    </div>

</main>


<?php

require_once "../../layouts/footer.php";

?>