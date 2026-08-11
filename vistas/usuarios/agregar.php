```php
<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/Rol.php";
require_once "../../modelos/Cargo.php";

$usuarioModel = new Usuario();
$rolModel = new Rol();
$cargoModel = new Cargo();

$roles = $rolModel->obtenerTodos();
$cargos = $cargoModel->obtenerTodos();

$idRolEmpleado = $usuarioModel->obtenerIdRolEmpleado()["id_rol"];
$idRolCliente = $usuarioModel->obtenerIdRolCliente()["id_rol"];

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Agregar usuario
        </h1>

        <p>
            Complete los datos para registrar un nuevo usuario.
        </p>

    </div>


    <div class="form-card">

        <form
            class="form"
            action="../../controladores/UsuarioController.php"
            method="POST"
            autocomplete="off">


            <input
                type="hidden"
                name="accion"
                value="agregar">


            <!-- =============================
                 DATOS PERSONALES
            ============================== -->

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


            <!-- =============================
                 CONTACTO Y ROL
            ============================== -->

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

                    <select
                        id="rol"
                        name="id_rol"
                        class="filter"
                        required>

                        <option value="">
                            Seleccione un rol
                        </option>

                        <?php foreach ($roles as $r) { ?>

                            <option
                                value="<?= $r["id_rol"] ?>">

                                <?= htmlspecialchars($r["nombre_rol"]) ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

            </div>


            <!-- =============================
                 DOCUMENTO Y TELÉFONO
            ============================== -->

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Documento
                    </label>

                    <input
                        type="text"
                        name="documento"
                        class="input"
                        maxlength="20">

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


            <!-- =============================
                 CONTRASEÑA
            ============================== -->

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


            <!-- =============================
                 DATOS EMPLEADO
            ============================== -->

            <div
                id="datosEmpleado"
                class="card"
                style="display:none; margin-top:30px;">

                <div class="card-header">

                    <div>

                        <h2>
                            Datos del empleado
                        </h2>

                        <p>
                            Información laboral y cargos del empleado.
                        </p>

                    </div>

                </div>


                <!-- =============================
                     DIRECCIÓN Y SALARIO
                ============================== -->

                <div class="form-row">

                    <div class="form-group">

                        <label>
                            Dirección
                        </label>

                        <input
                            type="text"
                            name="direccion"
                            class="input"
                            maxlength="255">

                    </div>


                    <div class="form-group">

                        <label>
                            Salario
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="salario"
                            class="input">

                    </div>

                </div>


                <!-- =============================
                     CARGOS
                ============================== -->

                <div class="form-group">

                    <label>
                        Cargos del empleado
                    </label>

                    <p style="margin-bottom:15px; color:#888;">
                        Seleccione uno o varios cargos que pueda desempeñar.
                    </p>


                    <div
                        class="cargos-container"
                        style="
                            display:grid;
                            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
                            gap:10px;
                        ">

                        <?php foreach ($cargos as $cargo) { ?>

                            <label
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    padding:12px;
                                    border:1px solid rgba(255,255,255,0.08);
                                    border-radius:8px;
                                    cursor:pointer;
                                ">

                                <input
                                    type="checkbox"
                                    name="cargos[]"
                                    value="<?= $cargo["id_cargo"] ?>">

                                <span>
                                    <?= htmlspecialchars($cargo["nombre_cargo"]) ?>
                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>

            </div>


            <!-- =============================
                 ACCIONES
            ============================== -->

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

                    Guardar usuario

                </button>

            </div>

        </form>

    </div>

</main>


<script>

const rol = document.getElementById("rol");

const empleado = document.getElementById("datosEmpleado");

const camposEmpleado = empleado.querySelectorAll(
    "input[name='direccion'], input[name='salario'], input[name='cargos[]']"
);


function actualizarDatosEmpleado() {

    if (rol.value == "<?= $idRolEmpleado ?>") {

        empleado.style.display = "block";

    } else {

        empleado.style.display = "none";

        /*
         * Si deja de ser empleado,
         * limpiamos los cargos seleccionados.
         */

        empleado
            .querySelectorAll("input[name='cargos[]']")
            .forEach(function(checkbox) {

                checkbox.checked = false;

            });

    }

}


rol.addEventListener(
    "change",
    actualizarDatosEmpleado
);

</script>


<?php

require_once "../../layouts/footer.php";

?>
```
