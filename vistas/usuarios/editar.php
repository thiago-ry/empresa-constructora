```php
<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/Rol.php";
require_once "../../modelos/Cargo.php";

$usuarioModel = new Usuario();
$rolModel = new Rol();
$cargoModel = new Cargo();


$id = $_GET["id"] ?? 0;


$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {

    header("Location: index.php");
    exit();

}


$roles = $rolModel->obtenerTodos();

$cargos = $cargoModel->obtenerTodos();


$idRolEmpleado = $usuarioModel->obtenerIdRolEmpleado()["id_rol"];
$idRolCliente = $usuarioModel->obtenerIdRolCliente()["id_rol"];


/*
========================================
CARGOS ACTUALES DEL EMPLEADO
========================================
*/

$cargosEmpleado = [];

if ($usuario["id_rol"] == $idRolEmpleado) {

    $cargosEmpleado = $usuarioModel->obtenerIdsCargosEmpleado($id);

}




require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-title">

        <h1>
            Editar usuario
        </h1>

        <p>
            Modifique los datos del usuario.
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
                value="editar">


            <input
                type="hidden"
                name="id_usuario"
                value="<?= $usuario["id_usuario"] ?>">


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
                        value="<?= htmlspecialchars($usuario["nombre"] ?? "") ?>"
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
                        value="<?= htmlspecialchars($usuario["apellido"] ?? "") ?>"
                        required>

                </div>


            </div>


            <!-- =============================
                 CORREO Y ROL
            ============================== -->

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
                        value="<?= htmlspecialchars($usuario["correo"] ?? "") ?>"
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


                        <?php foreach ($roles as $r) { ?>

                            <option
                                value="<?= $r["id_rol"] ?>"
                                <?= $usuario["id_rol"] == $r["id_rol"] ? "selected" : "" ?>>

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
                        maxlength="20"
                        value="<?= htmlspecialchars($usuario["documento"] ?? "") ?>">

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
                        value="<?= htmlspecialchars($usuario["telefono"] ?? "") ?>">

                </div>


            </div>


            <!-- =============================
                 DATOS EMPLEADO
            ============================== -->

            <div
                id="datosEmpleado"
                class="card"
                style="<?= $usuario["id_rol"] == $idRolEmpleado ? "" : "display:none;" ?>">


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
                            maxlength="255"
                            value="<?= htmlspecialchars($usuario["direccion"] ?? "") ?>">


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
                            class="input"
                            value="<?= htmlspecialchars($usuario["salario"] ?? "") ?>">


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
                                    value="<?= $cargo["id_cargo"] ?>"
                                    <?= in_array(
                                        $cargo["id_cargo"],
                                        $cargosEmpleado
                                    ) ? "checked" : "" ?>>


                                <span>

                                    <?= htmlspecialchars(
                                        $cargo["nombre_cargo"]
                                    ) ?>

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
                    type="submit"
                    class="btn btn-warning">


                    <i class="fa-solid fa-pen"></i>

                    Guardar cambios


                </button>


            </div>


        </form>


    </div>


</main>


<script>

const rol = document.getElementById("rol");

const empleado = document.getElementById("datosEmpleado");


function actualizarDatosEmpleado() {

    if (rol.value == "<?= $idRolEmpleado ?>") {

        empleado.style.display = "block";

    } else {

        empleado.style.display = "none";

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
