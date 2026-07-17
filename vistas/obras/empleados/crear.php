<?php

if (!isset($empleadosDisponibles)) {

    header("Location: ../../../controladores/EmpleadoObraController.php?accion=crear&id_obra=" . $_GET["id_obra"]);

    exit();
}

?>


<?php

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-header">


        <div>

            <h1 class="page-title">

                Asignar empleado

            </h1>


            <p>

                Registrar un nuevo empleado dentro de la obra.

            </p>


        </div>



        <a

            href="index.php?id_obra=<?= $_GET["id_obra"] ?>"

            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>



    </div>





    <div class="table-container">



        <div class="table-header">


            <h2>

                Datos de asignación

            </h2>


        </div>





        <form method="POST" action="/empresa_constructora/controladores/EmpleadoObraController.php">



            <input type="hidden" name="accion" value="agregar">


            <input type="hidden" name="id_obra" value="<?= $_GET["id_obra"] ?>">





            <div class="form-grid">



                <div class="form-group">


                    <label>

                        Empleado

                    </label>



                    <select

                        name="id_empleado"

                        required

                        class="form-control">


                        <option value="">

                            Seleccione un empleado

                        </option>



                        <?php foreach ($empleadosDisponibles as $e): ?>


                            <option value="<?= $e["id_empleado"] ?>">


                                <?= htmlspecialchars($e["apellido"]) ?>,

                                <?= htmlspecialchars($e["nombre"]) ?>


                                - DNI:

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

                        value="<?= date("Y-m-d") ?>"

                        required

                        class="form-control">


                </div>





                <div class="form-group">


                    <label>

                        Observaciones

                    </label>



                    <textarea

                        name="observaciones"

                        class="form-control"

                        rows="4"

                        placeholder="Observaciones del empleado">

</textarea>



                </div>



            </div>






            <br>



            <button

                type="submit"

                class="btn btn-primary">


                <i class="fa-solid fa-save"></i>

                Guardar


            </button>




            <a

                    href="listar.php?id_obra=<?= $_GET["id_obra"] ?>"

                class="btn btn-secondary">


                Cancelar


            </a>



        </form>




    </div>



</main>



<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>