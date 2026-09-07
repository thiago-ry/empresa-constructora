<?php

$herramientas = $herramientas ?? [];

$id_obra = $id_obra ?? $_GET["id_obra"] ?? 0;

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Asignar herramienta
            </h1>

            <p>
                Busque y seleccione una herramienta disponible para incorporarla a la obra.
            </p>

        </div>

    </div>


    <?php if (isset($_SESSION["error"])) { ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_SESSION["error"]) ?>

        </div>

        <?php unset($_SESSION["error"]); ?>

    <?php } ?>


    <div class="form-card">


        <!-- ==================================================
             BUSCADOR
        ================================================== -->

        <div class="form-group">

            <label for="buscarHerramienta">

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar herramienta

            </label>


            <div style="position:relative;">

                <input
                    type="text"
                    id="buscarHerramienta"
                    class="input"
                    placeholder="Buscar por nombre o descripción..."
                    autocomplete="off">


                <span
                    id="indicadorBusqueda"
                    style="
                        position:absolute;
                        right:15px;
                        top:50%;
                        transform:translateY(-50%);
                        display:none;
                    ">

                    <i class="fa-solid fa-spinner fa-spin"></i>

                </span>

            </div>


            <small style="display:block;margin-top:8px;">

                Utilice el buscador para filtrar herramientas disponibles.

            </small>

        </div>



        <!-- ==================================================
             TABLA DE HERRAMIENTAS
        ================================================== -->

        <div
            class="table-container"
            style="margin-top:20px;">

            <div class="table-header">

                <h2>

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                    Herramientas disponibles

                </h2>


                <span id="contadorHerramientas">

                    <?= count($herramientas) ?> herramientas

                </span>

            </div>


            <div style="overflow-x:auto;">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Herramienta
                            </th>

                            <th>
                                Descripción
                            </th>

                            <th>
                                Disponibles
                            </th>

                            <th class="no-print">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <tbody id="tablaHerramientas">

                        <?php if (empty($herramientas)) { ?>

                            <tr>

                                <td
                                    colspan="4"
                                    style="text-align:center;padding:40px;">

                                    <i class="fa-solid fa-screwdriver-wrench fa-2x"></i>

                                    <br><br>

                                    No hay herramientas disponibles para asignar.

                                </td>

                            </tr>

                        <?php } else { ?>


                            <?php foreach ($herramientas as $herramienta) { ?>

                                <tr
                                    class="fila-herramienta"
                                    data-nombre="<?= htmlspecialchars(strtolower($herramienta["nombre"])) ?>"
                                    data-descripcion="<?= htmlspecialchars(strtolower($herramienta["descripcion"] ?? "")) ?>">

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($herramienta["nombre"]) ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($herramienta["descripcion"] ?? "Sin descripción") ?>

                                    </td>


                                    <td>

                                        <span class="badge badge-success">

                                            <?= (int) $herramienta["disponibles"] ?>

                                            disponibles

                                        </span>

                                    </td>


                                    <td class="no-print">

                                        <button
                                            type="button"
                                            class="btn btn-primary btn-seleccionar-herramienta"
                                            data-id="<?= (int) $herramienta["id_herramienta"] ?>"
                                            data-nombre="<?= htmlspecialchars($herramienta["nombre"]) ?>"
                                            data-disponibles="<?= (int) $herramienta["disponibles"] ?>">

                                            <i class="fa-solid fa-check"></i>

                                            Seleccionar

                                        </button>

                                    </td>

                                </tr>

                            <?php } ?>


                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- ==================================================
             HERRAMIENTA SELECCIONADA
        ================================================== -->

        <div
            id="herramientaSeleccionada"
            style="
                display:none;
                margin-top:25px;
            ">

            <div
                style="
                    padding:20px;
                    border-radius:10px;
                    border:1px solid rgba(255,255,255,.1);
                ">


                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:15px;
                    ">


                    <div
                        style="
                            width:45px;
                            height:45px;
                            min-width:45px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                    </div>


                    <div>

                        <strong>
                            Herramienta seleccionada
                        </strong>


                        <div id="nombreHerramientaSeleccionada">
                        </div>


                        <small id="disponiblesHerramientaSeleccionada">
                        </small>

                    </div>


                    <button
                        type="button"
                        id="quitarHerramienta"
                        class="btn btn-danger"
                        style="margin-left:auto;">

                        <i class="fa-solid fa-xmark"></i>

                        Cambiar

                    </button>

                </div>

            </div>

        </div>



        <!-- ==================================================
             FORMULARIO
        ================================================== -->

        <form
            class="form"
            id="formAsignarHerramienta"
            action="/empresa_constructora/controladores/HerramientaObraController.php?accion=guardar"
            method="POST"
            autocomplete="off"
            style="margin-top:25px;">


            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($id_obra) ?>">


            <input
                type="hidden"
                name="id_herramienta"
                id="id_herramienta"
                value="">


            <!-- ==================================================
                 CANTIDAD
            ================================================== -->

            <div class="form-row">

                <div class="form-group">

                    <label for="cantidad">

                        <i class="fa-solid fa-hashtag"></i>

                        Cantidad a asignar

                    </label>


                    <input
                        type="number"
                        id="cantidad"
                        name="cantidad"
                        class="input"
                        min="1"
                        value="1"
                        required>


                    <small
                        id="mensajeCantidad"
                        style="
                            display:block;
                            margin-top:8px;
                        ">

                        Seleccione una herramienta para consultar la cantidad disponible.

                    </small>

                </div>


                <!-- ==================================================
                     FECHA
                ================================================== -->

                <div class="form-group">

                    <label for="fecha_asignacion">

                        Fecha de asignación

                    </label>


                    <input
                        type="date"
                        id="fecha_asignacion"
                        name="fecha_asignacion"
                        class="input"
                        value="<?= date("Y-m-d") ?>"
                        required>

                </div>

            </div>



            <!-- ==================================================
                 OBSERVACIONES
            ================================================== -->

            <div class="form-group">

                <label for="observaciones">

                    Observaciones

                </label>


                <textarea
                    id="observaciones"
                    name="observaciones"
                    class="input"
                    rows="4"
                    placeholder="Ingrese observaciones sobre la asignación"></textarea>

            </div>



            <!-- ==================================================
                 BOTONES
            ================================================== -->

            <div class="form-actions">


                <a
                    href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= htmlspecialchars($id_obra) ?>"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>


                <button
                    type="reset"
                    id="btnLimpiar"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>


                <button
                    type="submit"
                    id="btnAsignar"
                    class="btn btn-primary"
                    disabled>

                    <i class="fa-solid fa-screwdriver-wrench"></i>

                    Asignar herramienta

                </button>


            </div>

        </form>

    </div>

</main>


<script>

    const idObra = <?= json_encode($id_obra) ?>;

</script>


<?php

$script = "herramienta-obra";

require_once __DIR__ . "/../../../layouts/footer.php";

?>