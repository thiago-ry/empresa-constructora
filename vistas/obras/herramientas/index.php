<?php


if (!isset($herramientas)) {


    header("Location: ../../../controladores/HerramientaObraController.php?accion=listar&id_obra=" . $_GET["id_obra"]);

    exit();
}



$cantidad = count($herramientas);



require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";


?>


<main class="content">


    <div class="page-header">


        <div>


            <h1 class="page-title">

                Herramientas de Obra

            </h1>


            <p class="page-subtitle">

                Control de herramientas asignadas a la obra.

            </p>


        </div>



        <a
            href="/empresa_constructora/vistas/obras/ver.php?id=<?= $_GET["id_obra"] ?>"
            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>


    </div>





    <div class="cards-grid">


        <div class="dashboard-card">


            <h3>

                Total de herramientas

            </h3>


            <h2>

                <?= $cantidad ?>

            </h2>


        </div>



    </div>





    <div class="table-container">


        <div class="table-header">


            <h2>

                Herramientas asignadas

            </h2>



            <div>


                <button
                    onclick="window.print()"
                    class="btn btn-primary">


                    <i class="fa-solid fa-print"></i>


                </button>



                <a
                    href="/empresa_constructora/controladores/HerramientaObraController.php?accion=crear&id_obra=<?= $_GET["id_obra"] ?>"
                    class="btn btn-primary">


                    <i class="fa-solid fa-plus"></i>


                </a>


            </div>


        </div>





        <?php if (count($herramientas) > 0) { ?>



            <table class="table">


                <thead>


                    <tr>


                        <th>
                            Herramienta
                        </th>


                        <th>
                            Cantidad
                        </th>


                        <th>
                            Fecha asignación
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



                    <?php foreach ($herramientas as $herramienta) { ?>



                        <tr>


                            <td>

                                <?= htmlspecialchars($herramienta["herramienta"]) ?>

                            </td>



                            <td>

                                <?= htmlspecialchars($herramienta["cantidad"]) ?>

                            </td>



                            <td>

                                <?= date("d/m/Y", strtotime($herramienta["fecha_asignacion"])) ?>

                            </td>

                            <td>

                                <?php

                                $clase = "badge-secondary";

                                switch ($herramienta["estado"]) {

                                    case "Asignada":
                                        $clase = "badge-warning";
                                        break;

                                    case "Devuelta":
                                        $clase = "badge-success";
                                        break;

                                    case "En reparación":
                                        $clase = "badge-danger";
                                        break;
                                }

                                ?>

                                <span class="badge <?= $clase ?>">

                                    <?= htmlspecialchars($herramienta["estado"]) ?>

                                </span>

                            </td>

                            <td class="no-print">


                                <?php if ($herramienta["estado"] == "Asignada") { ?>

                                    <a
                                        href="/empresa_constructora/controladores/HerramientaObraController.php?accion=editar&id=<?= $herramienta["id_herramienta_obra"] ?>&id_obra=<?= $_GET["id_obra"] ?>"
                                        class="btn btn-warning">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>

                                <?php } else { ?>

                                    <a
                                        href="/empresa_constructora/controladores/HerramientaObraController.php?accion=editar&id=<?= $herramienta["id_herramienta_obra"] ?>&id_obra=<?= $_GET["id_obra"] ?>"
                                        class="btn btn-secondary">

                                        <i class="fa-solid fa-eye"></i>

                                    </a>

                                <?php } ?>


                            </td>


                        </tr>



                    <?php } ?>


                </tbody>


            </table>



        <?php } else { ?>



            <div style="padding:40px;text-align:center;">


                <i class="fa-solid fa-screwdriver-wrench fa-3x"></i>


                <br><br>


                <h3>

                    Todavía no hay herramientas asignadas.

                </h3>


                <p>

                    Presione el botón <strong>+</strong> para asignar una herramienta a la obra.

                </p>


            </div>



        <?php } ?>



    </div>



</main>



<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>