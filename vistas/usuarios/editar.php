<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/Rol.php";

$usuarioModel = new Usuario();
$rolModel = new Rol();


$id = $_GET["id"];


$usuario = $usuarioModel->buscarPorId($id);

$roles = $rolModel->obtenerTodos();


$idRolEmpleado = $usuarioModel->obtenerIdRolEmpleado()["id_rol"];

$idRolCliente = $usuarioModel->obtenerIdRolCliente()["id_rol"];


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
                value="<?= $usuario["id_usuario"]; ?>">





            <div class="form-row">


                <div class="form-group">

                    <label>
                        Nombre
                    </label>


                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        value="<?= $usuario["nombre"]; ?>"
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
                        value="<?= $usuario["apellido"]; ?>"
                        required>


                </div>


            </div>







            <div class="form-row">


                <div class="form-group">

                    <label>
                        Correo
                    </label>


                    <input
                        type="email"
                        name="correo"
                        class="input"
                        value="<?= $usuario["correo"]; ?>"
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
                                value="<?= $r["id_rol"]; ?>"
                                <?= $usuario["id_rol"] == $r["id_rol"] ? "selected" : ""; ?>>


                                <?= $r["nombre_rol"]; ?>


                            </option>


                        <?php } ?>


                    </select>


                </div>


            </div>
            
                <div class="form-row">


                    <div class="form-group">


                        <label>
                            Documento
                        </label>


                        <input
                            type="text"
                            name="documento"
                            class="input"
                            value="<?= $usuario["documento"] ?? ""; ?>">



                    </div>




                    <div class="form-group">


                        <label>
                            Teléfono
                        </label>


                        <input
                            type="text"
                            name="telefono"
                            class="input"
                            value="<?= $usuario["telefono"] ?? ""; ?>">



                    </div>


                </div>
                








            <!-- DATOS EMPLEADO -->


            <div
                id="datosEmpleado"
                class="card"
                style="<?= $usuario["id_rol"] == $idRolEmpleado ? "" : "display:none;"; ?>">



                <div class="card-header">


                    <div>

                        <h2>
                            Datos del empleado
                        </h2>


                        <p>
                            Información laboral del usuario.
                        </p>


                    </div>


                </div>



                <div class="form-row">


                    <div class="form-group">


                        <label>
                            Dirección
                        </label>


                        <input
                            type="text"
                            name="direccion"
                            class="input"
                            value="<?= $usuario["direccion"] ?? ""; ?>">



                    </div>





                    <div class="form-group">


                        <label>
                            Salario
                        </label>


                        <input
                            type="number"
                            step="0.01"
                            name="salario"
                            class="input"
                            value="<?= $usuario["salario"] ?? ""; ?>">



                    </div>


                </div>



            </div>








            <!-- DATOS CLIENTE -->

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




    rol.addEventListener("change", function() {


        empleado.style.display = "none";




        if (this.value == "<?= $idRolEmpleado ?>") {


            empleado.style.display = "block";


        }



    });
</script>





<?php

require_once "../../layouts/footer.php";

?>