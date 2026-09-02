<?php

require_once "../../modelos/Usuario.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

$usuarioModel = new Usuario();

$cargos = $usuarioModel->obtenerCargos();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Agregar empleado
        </h1>

        <p>
            Complete los datos para registrar un nuevo empleado.
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
                value="agregar">


            <!-- ==========================
                 DATOS PERSONALES
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
                        required>

                </div>

            </div>


            <!-- ==========================
                 CORREO Y ROL
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="correo"
                        class="input"
                        maxlength="150"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Rol
                    </label>

                    <span class="input">

                        <p
                            style="
                                margin-top: 13px;
                                font-size: 16px;
                            ">

                            Empleado

                        </p>

                    </span>

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
                        maxlength="30">

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
                        maxlength="200">

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
                        placeholder="0.00"
                        required>

                </div>

            </div>


            <!-- ==========================
                 CARGOS
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Cargo
                    </label>

                    <select
                        name="cargos[]"
                        class="input"
                        multiple
                        required
                        style="height: 120px;">

                        <?php foreach ($cargos as $cargo) { ?>

                            <option
                                value="<?= $cargo["id_cargo"]; ?>">

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
                 CONTRASEÑA
            =========================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        maxlength="255"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="confirmar"
                        class="input"
                        maxlength="255"
                        required>

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
                    type="reset"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar empleado

                </button>

            </div>

        </form>

    </div>

</main>


<?php

require_once "../../layouts/footer.php";

?>