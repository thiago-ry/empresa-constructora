<?php

require_once __DIR__ . "/../../config/permisos.php";

verificarPermiso("herramientas");

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                <?= strtoupper(htmlspecialchars($herramienta["nombre"])) ?>
            </h1>

            <p class="page-subtitle">
                Información general de la herramienta.
            </p>

        </div>

        <div>

            <a
                href="/empresa_constructora/controladores/HerramientaController.php?accion=editar&id=<?= $herramienta["id_herramienta"] ?>"
                class="btn btn-warning">

                <i class="fa-solid fa-pen"></i>

                Editar

            </a>

            <a
                href="/empresa_constructora/vistas/herramientas/"
                class="btn btn-secondary">

                <i class="fa-solid fa-arrow-left"></i>

                Volver

            </a>

        </div>

    </div>



    <div class="cards-grid">

        <div class="dashboard-card">

            <i class="fa-solid fa-toolbox"></i>

            <h3>Total de unidades</h3>

            <h2>

                <?= $herramienta["cantidad_total"] ?>

            </h2>

        </div>



        <div class="dashboard-card">

            <i class="fa-solid fa-check-circle"></i>

            <h3>Disponibles</h3>

            <h2>

                <?= $herramienta["cantidad_disponible"] ?>

            </h2>

        </div>



        <div class="dashboard-card">

            <i class="fa-solid fa-helmet-safety"></i>

            <h3>Asignadas</h3>

            <h2>

                <?= $herramienta["cantidad_asignada"] ?>

            </h2>

        </div>

    </div>




    <div class="card">

        <h2 style="margin-bottom:25px;">

            Información General

        </h2>

        <div class="info-card">

            <div class="info-item">

                <span>Nombre</span>

                <strong>

                    <?= htmlspecialchars($herramienta["nombre"]) ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Tipo</span>

                <strong>

                    <?= htmlspecialchars($herramienta["tipo"]) ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Marca</span>

                <strong>

                    <?= htmlspecialchars($herramienta["marca"] ?: "-") ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Modelo</span>

                <strong>

                    <?= htmlspecialchars($herramienta["modelo"] ?: "-") ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Cantidad registrada</span>

                <strong>

                    <?= $herramienta["cantidad_total"] ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Costo</span>

                <strong>

                    $ <?= number_format($herramienta["costo"], 2, ",", ".") ?>

                </strong>

            </div>



            <div class="info-item">

                <span>Fecha de adquisición</span>

                <strong>

                    <?= !empty($herramienta["fecha_adquisicion"])
                        ? date("d/m/Y", strtotime($herramienta["fecha_adquisicion"]))
                        : "-" ?>

                </strong>

            </div>

        </div>

    </div>





    <div class="card">

        <h2 style="margin-bottom:25px;">

            Módulos relacionados

        </h2>

        <div class="cards-grid">

            <div class="module-card">

                <i class="fa-solid fa-boxes-stacked"></i>

                <h3>

                    Unidades

                </h3>

                <p>

                    Ver todas las unidades individuales de esta herramienta.

                </p>

                <a
                    href="#"
                    class="btn btn-primary">

                    Ver unidades

                </a>

            </div>



            <div class="module-card">

                <i class="fa-solid fa-clock-rotate-left"></i>

                <h3>

                    Historial de asignaciones

                </h3>

                <p>

                    Consulte las obras donde fue utilizada la herramienta.

                </p>

                <a
                    href="#"
                    class="btn btn-primary">

                    Ver historial

                </a>

            </div>



            <div class="module-card">

                <i class="fa-solid fa-screwdriver-wrench"></i>

                <h3>

                    Mantenimientos

                </h3>

                <p>

                    Registro de reparaciones y mantenimientos realizados.

                </p>

                <a
                    href="#"
                    class="btn btn-primary">

                    Ver mantenimientos

                </a>

            </div>

        </div>

    </div>

</main>

<?php

require_once __DIR__ . "/../../layouts/footer.php";

?>