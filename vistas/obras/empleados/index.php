<?php

if (!isset($empleados)) {

    header("Location: ../../../controladores/EmpleadoObraController.php?accion=listar&id_obra=" . $_GET["id_obra"]);

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

                Empleados de la Obra

            </h1>


            <p>

                Gestión del personal asignado a la obra.

            </p>


        </div>



        <a

            href="/empresa_constructora/vistas/obras/ver.php?id=<?= $_GET["id_obra"] ?>"

            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>


    </div>





    <div class="table-container">



        <div class="table-header">


            <h2>

                Listado de empleados

            </h2>



            <div>


                <button

                    onclick="window.print()"

                    class="btn btn-primary">


                    <i class="fa-solid fa-print"></i>


                </button>




 <a

href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=crear&id_obra=<?= $_GET["id_obra"] ?>"

class="btn btn-primary">


<i class="fa-solid fa-plus"></i>


</a>


            </div>



        </div>






        <?php if (count($empleados) > 0) { ?>



            <table class="table">


                <thead>


                    <tr>

                        <th>
                            Empleado
                        </th>


                        <th>
                            Documento
                        </th>


                        <th>
                            Teléfono
                        </th>


                        <th>
                            Ingreso
                        </th>


                        <th>
                            Estado
                        </th>


                        <th class="no-print">
                            Acciones
                        </th>


                    </tr>


                </thead>




                <tbody>



                    <?php foreach ($empleados as $empleado): ?>



                        <tr>



                            <td>

                                <?= htmlspecialchars($empleado["apellido"]) ?>,

                                <?= htmlspecialchars($empleado["nombre"]) ?>


                            </td>



                            <td>

                                <?= htmlspecialchars($empleado["documento"]) ?>

                            </td>




                            <td>

                                <?= htmlspecialchars($empleado["telefono"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($empleado["fecha_ingreso"]) ?>

                            </td>




                            <td>


                                <span class="badge">

                                    <?= htmlspecialchars($empleado["estado"]) ?>

                                </span>


                            </td>




                            <td>


                                <a

                                    href="editar.php?id=<?= $empleado["id_empleado_obra"] ?>"

                                    class="btn btn-warning">


                                    <i class="fa-solid fa-pen"></i>


                                </a>




                                <?php if ($empleado["estado"] == "Activo") { ?>


                                    <a

                                        href="retirar.php?id=<?= $empleado["id_empleado_obra"] ?>"

                                        class="btn btn-danger">


                                        <i class="fa-solid fa-user-minus"></i>


                                    </a>



                                <?php } ?>



                            </td>



                        </tr>



                    <?php endforeach; ?>



                </tbody>



            </table>



        <?php } else { ?>



            <div style="padding:40px;text-align:center;">


                <i class="fa-solid fa-users fa-3x"></i>


                <br><br>



                <h3>

                    Todavía no hay empleados asignados.

                </h3>



                <p>

                    Presione el botón <strong>+</strong> para asignar el primer empleado.

                </p>



            </div>



        <?php } ?>



    </div>



</main>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>