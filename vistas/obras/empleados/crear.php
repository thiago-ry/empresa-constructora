<?php

if (!isset($empleadosDisponibles)) {

    header("Location: ../../../controladores/EmpleadoObraController.php?accion=crear&id_obra=" . $_GET["id_obra"]);

    exit();

}


require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-title">


        <h1>
            Asignar empleado
        </h1>


        <p>
            Seleccione un empleado activo para incorporarlo a la obra.
        </p>


    </div>




    <div class="form-card">


        <form

            class="form"

            action="/empresa_constructora/controladores/EmpleadoObraController.php"

            method="POST"

            autocomplete="off">



            <input
                type="hidden"
                name="accion"
                value="agregar">


            <input
                type="hidden"
                name="id_obra"
                value="<?= $_GET["id_obra"] ?>">





            <div class="form-row">



                <div class="form-group">


                    <label>

                        Empleado

                    </label>



                    <select

                        name="id_usuario"

                        class="filter"

                        required>


                        <option value="">

                            Seleccione un empleado

                        </option>



                        <?php foreach ($empleadosDisponibles as $e): ?>


                            <option

                                value="<?= $e["id_usuario"] ?>">


                                <?= htmlspecialchars($e["apellido"]) ?>

                                <?= htmlspecialchars($e["nombre"]) ?>

                                -

                                DNI:

                                <?= htmlspecialchars($e["documento"]) ?>


                            </option>


                        <?php endforeach; ?>


                    </select>


                </div>





                <div class="form-group">


                    <label>

                        Fecha de ingreso

                    </label>



                    <input

                        type="date"

                        name="fecha_ingreso"

                        class="input"

                        value="<?= date("Y-m-d") ?>"

                        required>



                </div>



            </div>







            <div class="form-group">


                <label>

                    Observaciones

                </label>



                <textarea

                    name="observaciones"

                    class="input"

                    rows="4"

                    placeholder="Ingrese observaciones sobre la asignación"></textarea>


            </div>






            <div class="form-actions">





   <a
    href="http://localhost/empresa_constructora/controladores/EmpleadoObraController.php?accion=listar&id_obra=<?= $_GET["id_obra"] ?>"
    class="btn btn-secondary">

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


                    <i class="fa-solid fa-user-plus"></i>


                    Asignar empleado


                </button>





            </div>




        </form>



    </div>



</main>




<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>