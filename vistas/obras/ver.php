<?php

require_once "../../modelos/Obra.php";
require_once "../../config/permisos.php";
require_once "../../modelos/Etapa.php";

verificarPermiso("obras");

$obra = new Obra();

$id = $_GET["id"] ?? 0;

$detalle = $obra->buscarPorId($id);

$etapa = new Etapa();

$etapas = $etapa->obtenerPorObra($id);

$etapa = new Etapa();

$avance = $etapa->calcularAvance($id);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-header">

        <div>

            <h1 class="page-title">
                <?= htmlspecialchars($detalle["nombre_obra"]) ?>
            </h1>

            <p class="page-subtitle">
                Información general y módulos de gestión de la obra.
            </p>

        </div>


        <a href="index.php" class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>


    </div>




    <div class="card">


        <div class="card-header">


            <h2>
                Datos generales
            </h2>


            <span class="badge">

                <?= htmlspecialchars($detalle["estado"]) ?>

            </span>


        </div>




        <div class="form-grid">


            <div>

                <strong>Cliente</strong>

                <p>
                    <?= htmlspecialchars($detalle["nombre_cliente"]) ?>
                </p>

            </div>



<div>

    <strong>Avance de obra</strong>


    <p>
        <?= $avance ?>%
    </p>


    <div class="progress">

        <div class="progress-bar" 
             style="width: <?= $avance ?>%">
        </div>

    </div>


</div>



            <div>

                <strong>Fecha inicio</strong>

                <p>
                    <?= htmlspecialchars($detalle["fecha_inicio"]) ?>
                </p>

            </div>



            <div>

                <strong>Fecha finalización</strong>

                <p>
                    <?= htmlspecialchars($detalle["fecha_fin"]) ?>
                </p>

            </div>



            <div>

                <strong>Dirección</strong>

                <p>
                    <?= htmlspecialchars($detalle["direccion"]) ?>
                </p>

            </div>



            <div>

                <strong>Estado</strong>

                <p>
                    <?= htmlspecialchars($detalle["estado"]) ?>
                </p>

            </div>


        </div>




        <br>


        <strong>Descripción</strong>


        <p>

            <?= nl2br(htmlspecialchars($detalle["descripcion"])) ?>

        </p>



    </div>

    <div class="card">


    <div class="card-header">

        <h2>
            Etapas de la obra
        </h2>


        <a href="etapas/index.php?id_obra=<?= $detalle["id_obra"] ?>" 
           class="btn btn-primary">

            Gestionar etapas

        </a>

    </div>



    <?php if(count($etapas) > 0){ ?>


        <div class="table-container">


            <table class="table">


                <thead>

                    <tr>

                        <th>
                            Etapa
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Inicio
                        </th>

                        <th>
                            Fin
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach($etapas as $e){ ?>


                        <tr>


                            <td>
                                <?= htmlspecialchars($e["nombre_etapa"]) ?>
                            </td>


                            <td>

                                <span class="badge">

                                    <?= htmlspecialchars($e["estado"]) ?>

                                </span>

                            </td>


                            <td>
                                <?= $e["fecha_inicio"] ?>
                            </td>


                            <td>
                                <?= $e["fecha_fin"] ?>
                            </td>


                        </tr>


                    <?php } ?>


                </tbody>


            </table>


        </div>


    <?php }else{ ?>


        <p>
            No hay etapas registradas para esta obra.
        </p>


    <?php } ?>


</div>





    <div class="toolbar">

        <h2>
            Módulos de la obra
        </h2>

    </div>




    <div class="cards-grid">



        <div class="card">

            <i class="fa-solid fa-layer-group fa-2x"></i>


            <h3>
                Etapas
            </h3>


            <p>
                Gestionar las etapas constructivas.
            </p>


            <a href="etapas/index.php?id_obra=<?= $detalle["id_obra"] ?>" 
               class="btn btn-primary">

                Ingresar

            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-chart-line fa-2x"></i>


            <h3>
                Avances
            </h3>


            <p>
                Registrar avances diarios de obra.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-users fa-2x"></i>


            <h3>
                Empleados
            </h3>


            <p>
                Personal asignado a la obra.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-boxes-stacked fa-2x"></i>


            <h3>
                Materiales
            </h3>


            <p>
                Control de materiales utilizados.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-screwdriver-wrench fa-2x"></i>


            <h3>
                Herramientas
            </h3>


            <p>
                Herramientas asignadas.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-triangle-exclamation fa-2x"></i>


            <h3>
                Incidencias
            </h3>


            <p>
                Problemas registrados durante la obra.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-camera fa-2x"></i>


            <h3>
                Fotos
            </h3>


            <p>
                Registro fotográfico.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>




        <div class="card">

            <i class="fa-solid fa-folder-open fa-2x"></i>


            <h3>
                Documentos
            </h3>


            <p>
                Planos, contratos y archivos.
            </p>


            <a href="#" class="btn btn-primary">
                Próximamente
            </a>


        </div>



    </div>


</main>


<?php require_once "../../layouts/footer.php"; ?>