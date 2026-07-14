<?php

require_once "../../modelos/Obra.php";
require_once "../../modelos/Etapa.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();

$id = $_GET["id"] ?? 0;

$detalle = $obra->buscarPorId($id);

$etapa = new Etapa();

$etapas = $etapa->obtenerPorObra($id);

$avance = $etapa->calcularAvance($id);

$resumen = $etapa->obtenerResumen($id);

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
                Panel de control de la obra.
            </p>

        </div>

        <a href="index.php" class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>

            Volver

        </a>

    </div>


    <div class="card">

        <div class="card-header">

            <div>

                <h2>
                    Información general
                </h2>

                <p>
                    Datos principales de la obra.
                </p>

            </div>

            <span class="badge">
                <?= htmlspecialchars($detalle["estado"]) ?>
            </span>

        </div>


        <div class="info-grid">

            <div class="info-item">

                <span>
                    Cliente
                </span>

                <strong>
                    <?= htmlspecialchars($detalle["nombre_cliente"]) ?>
                </strong>

            </div>

            <div class="info-item">

                <span>
                    Dirección
                </span>

                <strong>
                    <?= htmlspecialchars($detalle["direccion"]) ?>
                </strong>

            </div>

            <div class="info-item">

                <span>
                    Fecha de inicio
                </span>

                <strong>
                    <?= htmlspecialchars($detalle["fecha_inicio"]) ?>
                </strong>

            </div>

            <div class="info-item">

                <span>
                    Fecha estimada de finalización
                </span>

                <strong>
                    <?= htmlspecialchars($detalle["fecha_fin"]) ?>
                </strong>

            </div>

        </div>

        <br>

        <strong>
            Descripción
        </strong>

        <p>

            <?= nl2br(htmlspecialchars($detalle["descripcion"])) ?>

        </p>

    </div>


    <div class="toolbar">

        <h2>
            Resumen de la obra
        </h2>

    </div>


    <div class="cards-grid">


        <div class="card">

            <i class="fa-solid fa-list-check fa-2x"></i>

            <h3>
                Total de etapas
            </h3>

            <h2>
                <?= $resumen["total"] ?>
            </h2>

        </div>


        <div class="card">

            <i class="fa-solid fa-circle-check fa-2x"></i>

            <h3>
                Finalizadas
            </h3>

            <h2>
                <?= $resumen["finalizadas"] ?>
            </h2>

        </div>


        <div class="card">

            <i class="fa-solid fa-person-digging fa-2x"></i>

            <h3>
                En proceso
            </h3>

            <h2>
                <?= $resumen["proceso"] ?>
            </h2>

        </div>


        <div class="card">

            <i class="fa-regular fa-clock fa-2x"></i>

            <h3>
                Pendientes
            </h3>

            <h2>
                <?= $resumen["pendientes"] ?>
            </h2>

        </div>


        <div class="card">

            <i class="fa-solid fa-chart-line fa-2x"></i>

            <h3>
                Avance general
            </h3>

            <h2>
                <?= $avance ?>%
            </h2>

            <div class="progress">

                <div
                    class="progress-bar"
                    style="width: <?= $avance ?>%;">
                </div>

            </div>

        </div>

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

            <a
                href="etapas/index.php?id_obra=<?= $detalle["id_obra"] ?>"
                class="btn btn-primary">

                Ingresar

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-chart-line fa-2x"></i>

            <h3>
                Avances diarios
            </h3>

            <a
                href="../../controladores/AvanceController.php?accion=listar&id_obra=<?= $detalle["id_obra"] ?>"
                class="btn btn-primary">

                Ingresar

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-users fa-2x"></i>

            <h3>
                Empleados
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-boxes-stacked fa-2x"></i>

            <h3>
                Materiales
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-screwdriver-wrench fa-2x"></i>

            <h3>
                Herramientas
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-triangle-exclamation fa-2x"></i>

            <h3>
                Incidencias
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-camera fa-2x"></i>

            <h3>
                Fotos
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>


        <div class="card">

            <i class="fa-solid fa-folder-open fa-2x"></i>

            <h3>
                Documentos
            </h3>

            <a
                href="#"
                class="btn btn-secondary">

                Próximamente

            </a>

        </div>

    </div>


    <div class="card">

        <div class="card-header">

            <h2>
                Etapas de la obra
            </h2>

        </div>

        <?php if (count($etapas) > 0) { ?>

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
                                Fecha inicio
                            </th>

                            <th>
                                Fecha fin
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($etapas as $e) { ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($e["nombre_etapa"]) ?>

                                </td>

                                <td>

                                    <?php

                                    $clase = "";

                                    switch ($e["estado"]) {

                                        case "Finalizada":
                                            $clase = "badge-success";
                                            break;

                                        case "Pendiente":
                                            $clase = "badge-danger";
                                            break;

                                        default:
                                            $clase = "";
                                            break;

                                    }

                                    ?>

                                    <span class="badge <?= $clase ?>">

                                        <?= htmlspecialchars($e["estado"]) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= htmlspecialchars($e["fecha_inicio"]) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($e["fecha_fin"]) ?>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <p>

                No existen etapas registradas para esta obra.

            </p>

        <?php } ?>

    </div>

</main>

<?php require_once "../../layouts/footer.php"; ?>