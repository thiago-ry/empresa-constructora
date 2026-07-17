<?php

require_once "../../modelos/Rol.php";

$rol = new Rol();
$roles = $rol->obtenerTodos();

$idRolEmpleado = 1;
$idRolCliente = 6;

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>Agregar usuario</h1>

        <p>Complete los datos para registrar un nuevo usuario.</p>

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

            <div class="form-row">

                <div class="form-group">

                    <label>Nombre</label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="100"
                        required>

                </div>

                <div class="form-group">

                    <label>Apellido</label>

                    <input
                        type="text"
                        name="apellido"
                        class="input"
                        maxlength="100"
                        required>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Correo electrónico</label>

                    <input
                        type="email"
                        name="correo"
                        class="input"
                        maxlength="150"
                        required>

                </div>

                <div class="form-group">

                    <label>Rol</label>

                    <select
                        id="rol"
                        name="rol"
                        class="filter"
                        required>

                        <option value="">
                            Seleccione un rol
                        </option>

                        <?php foreach($roles as $r){ ?>

                            <option value="<?= $r["id_rol"] ?>">

                                <?= $r["nombre_rol"] ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Contraseña</label>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        maxlength="255"
                        required>

                </div>

                <div class="form-group">

                    <label>Confirmar contraseña</label>

                    <input
                        type="password"
                        name="confirmar"
                        class="input"
                        maxlength="255"
                        required>

                </div>

            </div>


            <!-- ============================= -->
            <!-- DATOS EMPLEADO -->
            <!-- ============================= -->

            <div
                id="datosEmpleado"
                class="card"
                style="display:none;margin-top:30px;">

                <div class="card-header">

                    <div>

                        <h2>

                            Datos del empleado

                        </h2>

                        <p>

                            Información laboral.

                        </p>

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label>Documento</label>

                        <input
                            type="text"
                            name="documento"
                            class="input">

                    </div>

                    <div class="form-group">

                        <label>Teléfono</label>

                        <input
                            type="text"
                            name="telefono_empleado"
                            class="input">

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label>Dirección</label>

                        <input
                            type="text"
                            name="direccion_empleado"
                            class="input">

                    </div>

                    <div class="form-group">

                        <label>Salario</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="salario"
                            class="input">

                    </div>

                </div>

            </div>


            <!-- ============================= -->
            <!-- DATOS CLIENTE -->
            <!-- ============================= -->

            <div
                id="datosCliente"
                class="card"
                style="display:none;margin-top:30px;">

                <div class="card-header">

                    <div>

                        <h2>

                            Datos del cliente

                        </h2>

                        <p>

                            Información de contacto.

                        </p>

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">

                        <label>Teléfono</label>

                        <input
                            type="text"
                            name="telefono_cliente"
                            class="input">

                    </div>

                    <div class="form-group">

                        <label>Dirección</label>

                        <input
                            type="text"
                            name="direccion_cliente"
                            class="input">

                    </div>

                </div>

            </div>


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

const rol=document.getElementById("rol");

const empleado=document.getElementById("datosEmpleado");

const cliente=document.getElementById("datosCliente");

rol.addEventListener("change",function(){

    empleado.style.display="none";

    cliente.style.display="none";

    if(this.value=="<?= $idRolEmpleado ?>"){

        empleado.style.display="block";

    }

    if(this.value=="<?= $idRolCliente ?>"){

        cliente.style.display="block";

    }

});

</script>

<?php

require_once "../../layouts/footer.php";

?>